<?php
declare(strict_types=1);

use App\Controller\RestController;
use App\Crypto\CryptoInterface;
use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\InputFilter\InputFilter as AppInputFilter;
use App\Mail\adapter\SendinblueMailer;
use App\Mail\Contact;
use App\Mail\MailerInterface;
use App\Module\Oauth\Filter\PasswordFilter;
use App\Module\Organization\Storage\OrganizationStorage;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Organization\Validator\OrganizationSaveValidator;
use App\Module\Playlist\Entity\PlaylistEntity;
use App\Module\Playlist\Storage\PlaylistStorage;
use App\Module\Playlist\Storage\PlaylistStorageInterface;
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
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\InArray;
use Laminas\Validator\StringLength;
use MongoDB\Client as MongoClient;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([


        PlaylistStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['playlist'];

            $hydrator = $c->get('StoragePlaylistEntityHydrator')
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('PlaylistEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('PlaylistEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new PlaylistStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('PlaylistEntityPrototype'));

            return $storage;
        },
        'RestPlaylistEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            return $hydrator;
        },
        'StoragePlaylistEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
          
            return $hydrator;
        },
        'PlaylistEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new PlaylistEntity());
        },
        'PlaylistPostValidation' => function(ContainerInterface $container) {

            $inputFilter = new AppInputFilter();

            // Name field
            $name = new Input('name');
            // Last name field          

            $inputFilter
                ->add($name);

            return $inputFilter;
        }
    ]);
};
