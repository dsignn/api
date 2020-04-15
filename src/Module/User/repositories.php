<?php
declare(strict_types=1);

use App\Crypto\CryptoInterface;
use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Module\Oauth\Filter\PasswordFilter;
use App\Module\Resource\Entity\ImageResourceEntity;
use App\Module\Resource\Entity\VideoResourceEntity;
use App\Module\User\Entity\Embedded\RecoverPassword;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Event\UserPasswordEvent;
use App\Module\User\Mail\adapter\UserGoogleMailer;
use App\Module\User\Mail\RecoverPasswordMailerInterface;
use App\Module\User\Storage\UserStorage;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\MultiEntityPrototype;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use DI\ContainerBuilder;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\MethodMatchFilter;
use Laminas\Hydrator\Strategy\ClosureStrategy;
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

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addFilter('identifier', new MethodMatchFilter('getIdentifier'),  FilterComposite::CONDITION_AND);
            $recoverPasswordHydrator = new ClassMethodsHydrator();
            $recoverPasswordHydrator->addStrategy('date', new MongoDateStrategy());
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new RecoverPassword()));

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
        }
    ])->addDefinitions([
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
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new RecoverPassword()));

            return $hydrator;
        }
    ])->addDefinitions([
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
            $hydrator->addStrategy('recoverPassword', new HydratorStrategy($recoverPasswordHydrator, new RecoverPassword()));

            return $hydrator;
        }
    ])->addDefinitions([
        'UserEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new UserEntity());
        }
    ])->addDefinitions([
        'UserPostValidation' => function(ContainerInterface $container) {

            $inputFilter = new InputFilter();

            // Name field
            $name = new Input('name');
            // Last name field
            $lastName = new Input('lastName');
            // Email field
            $email= new Input('email');
            $email->getValidatorChain()->attach(new EmailAddress());
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

            $inputFilter->add($email)
                ->add($name)
                ->add($lastName)
                ->add($role)
                ->add($password);

            return $inputFilter;
        }
    ])->addDefinitions([
        'PasswordFilter' => function(ContainerInterface $container) {
            return new PasswordFilter($container->get('OAuthCrypto'));
        }
    ])->addDefinitions([
        CryptoInterface::class => function(ContainerInterface $container) {
            return $container->get('OAuthCrypto');
        }
    ])->addDefinitions([
        RecoverPasswordMailerInterface::class => function(ContainerInterface $container) {
            $settings = $container->get('settings');
            $serviceSetting = $settings['mail'];

            return new UserGoogleMailer($serviceSetting);
        }
    ]);
};
