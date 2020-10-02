<?php
declare(strict_types=1);

use App\Crypto\CryptoInterface;
use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Mail\Contact;
use App\Module\Oauth\Filter\PasswordFilter;
use App\Module\Organization\Validator\UniqueNameOrganization;
use App\Module\User\Entity\Embedded\ActivationCode;
use App\Module\User\Entity\Embedded\RecoverPassword;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Event\AppendOrganizationEvent;
use App\Module\User\Event\UserActivationCodeEvent;
use App\Module\User\Event\UserPasswordEvent;
use App\Module\User\Mail\adapter\UserGoogleMailer;
use App\Module\User\Mail\UserMailerInterface;
use App\Module\User\Storage\UserStorage;
use App\Module\User\Storage\UserStorageInterface;
use App\Module\User\Validator\EmailExistValidator;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\MethodMatchFilter;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\InArray;
use Laminas\Validator\StringLength;
use MongoDB\Client as MongoClient;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        UserStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['user'];

            $hydrator = $c->get('StorageUserEntityHydrator')
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('UserEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('UserEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new UserStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('UserEntityPrototype'));

            $storage->getEventManager()->attach(Storage::$BEFORE_SAVE, new UserPasswordEvent($c->get('OAuthCrypto')));

            $storage->getEventManager()->attach(
                Storage::$BEFORE_SAVE,
                new UserActivationCodeEvent($c->get('OAuthCrypto'), $c->get(UserMailerInterface::class), $c->get('UserFrom'), $settings['mail']['activationCode'])
            );

            $storage->getEventManager()->attach(Storage::$PREPROCESS_SAVE, new AppendOrganizationEvent(
                $c->get(Client::class),
                $c->get('settings')['httpClient']["url"],
                $c->get('RestOrganizationEntityHydrator')
            ));

            return $storage;
        },
        'RestUserEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addFilter('password', new MethodMatchFilter('getPassword'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('recoverPassword', new MethodMatchFilter('getRecoverPassword'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('activationCode', new MethodMatchFilter('getActivationCode'),  FilterComposite::CONDITION_AND);
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->setNamingStrategy(new CamelCaseStrategy());
            $recoverPasswordHydrator->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new SingleEntityPrototype(new RecoverPassword())));

            $activationCode = new ClassMethodsHydrator();
            $activationCode->setNamingStrategy(new CamelCaseStrategy());
            $activationCode->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('activationCode', new HydratorStrategy($activationCode, new SingleEntityPrototype(new ActivationCode())));

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $hydrator->addStrategy('organizations', new HydratorArrayStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));


            return $hydrator;
        },
        'StorageUserEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);

            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new SingleEntityPrototype(new RecoverPassword())));

            $activationCode = new ClassMethodsHydrator();
            $activationCode->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('activationCode', new HydratorStrategy($activationCode, new SingleEntityPrototype(new ActivationCode())));

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $hydrator->addStrategy('organizations', new HydratorArrayStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            return $hydrator;
        },
        'RpcPasswordUserEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->addFilter('password', new MethodMatchFilter('getPassword'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->addStrategy('date', $c->get('EntityDateRestStrategy'));
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new SingleEntityPrototype(new RecoverPassword())));

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('organizations', new HydratorArrayStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            return $hydrator;
        },
        'UserEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new UserEntity());
        },
        'UserPostValidation' => function(ContainerInterface $container) {

            $inputFilter = new InputFilter();

            // Name field
            $name = new Input('name');
            // Last name field
            $lastName = new Input('lastName');
            // Email field
            $email= new Input('email');
            $email->getValidatorChain()
                ->attach(new EmailAddress())
                ->attach($container->get(EmailExistValidator::class));

            $nameOrganization = new Input('nameOrganization');
            $nameOrganization->setRequired(false);
            $nameOrganization->getValidatorChain()
                ->attach($container->get(UniqueNameOrganization::class));

            // Role field
            $role = new Input('roleId');
            $role->getValidatorChain()->attach(new InArray([
                'haystack' => ['guest', 'restaurantOwner']
            ]));
            // Password field
            $password = $password = new Input('password');
            $password->getValidatorChain()->attach(new StringLength([
                'min' => 4,
                'max' => 12
            ]));

            $organizationsCollectionInputFilter = new CollectionInputFilter();
            $organizationsInputFilter = new InputFilter();

            $organizationId = new Input('id');
            $organizationId->setRequired(false);
            $organizationsInputFilter->add($organizationId);

            $organizationCollection = new Input('collection');
            $organizationCollection->setRequired(false);
            $organizationsInputFilter->add($organizationCollection);

            $organizationsCollectionInputFilter->setInputFilter($organizationsInputFilter);

            $inputFilter
                ->add($nameOrganization)
                ->add($email)
                ->add($name)
                ->add($lastName)
                ->add($role)
                ->add($password)
                ->add($organizationsCollectionInputFilter, 'organizations');

            return $inputFilter;
        },
        'PasswordFilter' => function(ContainerInterface $container) {
            return new PasswordFilter($container->get('OAuthCrypto'));
        },
        CryptoInterface::class => function(ContainerInterface $container) {
            return $container->get('OAuthCrypto');
        },
        UserMailerInterface::class => function(ContainerInterface $container) {
            $settings = $container->get('settings');
            $serviceSetting = $settings['mail'];

            return new UserGoogleMailer($serviceSetting);
        },
        EmailExistValidator::class => function(ContainerInterface $container) {
            return new EmailExistValidator($container->get(UserStorageInterface::class));
        },
        'UserFrom' => function(ContainerInterface $container) {
            $contact = new Contact();
            $arrayFrom = $container->get('settings')['mail']['from'];
            $contact->setName($arrayFrom['name']);
            $contact->setEmail($arrayFrom['email']);
            return $contact;
        }
    ]);
};
