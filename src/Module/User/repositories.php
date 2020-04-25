<?php
declare(strict_types=1);

use App\Crypto\CryptoInterface;
use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Module\Oauth\Filter\PasswordFilter;
use App\Module\User\Entity\Embedded\RecoverPassword;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Event\UserPasswordEvent;
use App\Module\User\Mail\adapter\UserGoogleMailer;
use App\Module\User\Mail\RecoverPasswordMailerInterface;
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
use Laminas\Filter\Callback;
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

            return $storage;
        },
        'RestUserEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->addFilter('password', new MethodMatchFilter('getPassword'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('recoverPassword', new MethodMatchFilter('getRecoverPassword'),  FilterComposite::CONDITION_AND);
            $hydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));
            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new SingleEntityPrototype(new RecoverPassword())));

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));
            $hydrator->addStrategy('organizations', new HydratorArrayStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));


            return $hydrator;
        },
        'StorageUserEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);
            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new SingleEntityPrototype(new RecoverPassword())));

            $organizationHydrator = new ClassMethodsHydrator();
            $hydrator->addStrategy('organizations', new HydratorArrayStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));

            return $hydrator;
        },
        'RpcPasswordUserEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->addFilter('password', new MethodMatchFilter('getPassword'),  FilterComposite::CONDITION_AND);
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);
            $hydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));
            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new SingleEntityPrototype(new RecoverPassword())));

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
                //->attach($container->get(EmailExistValidator::class))
            ;
            // Role field
            $role = new Input('role');
            $role->getValidatorChain()->attach(new InArray([
                'haystack' => ['guest', 'companyOwner', 'admin']
            ]));
            // Password field
            $password = $password = new Input('password');
            $password->getValidatorChain()->attach(new StringLength([
                'min' => 8,
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

            $inputFilter->add($email)
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
        RecoverPasswordMailerInterface::class => function(ContainerInterface $container) {
            $settings = $container->get('settings');
            $serviceSetting = $settings['mail'];

            return new UserGoogleMailer($serviceSetting);
        },
        EmailExistValidator::class => function(ContainerInterface $container) {
            return new EmailExistValidator($container->get(UserStorageInterface::class));
        }
    ]);
};
