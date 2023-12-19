<?php
declare(strict_types=1);

use App\Controller\RestController;
use App\Crypto\CryptoInterface;
use App\Crypto\CryptoOpenSsl;
use App\Filter\DefaultFilter;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\InputFilter\InputFilter as AppInputFilter;
use App\Mail\adapter\SendinblueMailer;
use App\Mail\Contact;
use App\Mail\MailerInterface;
use App\Module\Monitor\Entity\MonitorReference;
use App\Module\Oauth\Filter\PasswordFilter;
use App\Module\Organization\Storage\OrganizationStorage;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Organization\Validator\OrganizationSaveValidator;
use App\Module\Playlist\Entity\PlaylistEntity;
use App\Module\Playlist\Http\QueryString\PlaylistQueryString;
use App\Module\Playlist\Storage\PlaylistStorage;
use App\Module\Playlist\Storage\PlaylistStorageInterface;
use App\Module\User\Entity\Embedded\ActivationCode;
use App\Module\User\Entity\Embedded\RecoverPassword;
use App\Module\User\Entity\UserEntity;
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
use App\Validator\Mongo\ObjectIdValidator;
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
use Laminas\Validator\NotEmpty;
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

            $referenceHydrator = new ClassMethodsHydrator();
            $referenceHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $referenceHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->setNamingStrategy(new CamelCaseStrategy());
            $monitorHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $monitorHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $monitorHydrator->addStrategy('parentId', $c->get('MongoIdRestStrategy'));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('organizationReference', new HydratorStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('resources', new HydratorArrayStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('binds', new HydratorArrayStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('monitorContainerReference', new HydratorStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorReference())));
            
            return $hydrator;
        },
        'StoragePlaylistEntityHydrator' => function(ContainerInterface $c) {

            $referenceHydrator = new ClassMethodsHydrator();
            $referenceHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $referenceHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $monitorHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $monitorHydrator->addStrategy('parentId', $c->get('MongoIdStorageStrategy'));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('organizationReference', new HydratorStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('resources', new HydratorArrayStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('binds', new HydratorArrayStrategy($referenceHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('monitorContainerReference', new HydratorStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorReference())));

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

            $inputFilter->add($name);

            $collectionResourceInputFilter = new CollectionInputFilter();
            $resourceInputFilter = new InputFilter();

            $id = new Input('id');
            $id->getValidatorChain()
                ->attach(new ObjectIdValidator());

            
            $collection = new Input('collection');
            $collection->setRequired(false);
            $collection->getFilterChain()
                ->attach(new DefaultFilter('resource'));    

            $resourceInputFilter->add($id)
                ->add($collection);
            $collectionResourceInputFilter->setInputFilter($resourceInputFilter);

            $inputFilter->add($collectionResourceInputFilter, 'resources');


            $collectionBindInputFilter = new CollectionInputFilter();
            $bindInputFilter = new InputFilter();

            $id = new Input('id');
            $id->setRequired(false);
            $id->getValidatorChain()
                ->attach(new ObjectIdValidator());

            
            $collection = new Input('collection');
            $collection->setRequired(false);
            $collection->getFilterChain()
                ->attach(new DefaultFilter('playlist'));    

            $bindInputFilter->add($id)
                ->add($collection);
           
            $collectionBindInputFilter->setInputFilter($bindInputFilter);
            $inputFilter->add($collectionBindInputFilter, 'binds');

            $organizationReference = new InputFilter();

            $id = new Input('id');
            $id->setRequired(true);
            $id->getValidatorChain()
                ->attach(new NotEmpty())
                ->attach(new ObjectIdValidator());
            
            $organizationReference->add($id);

            $collection = new Input('collection');
            $collection->setRequired(false);
            $collection->getFilterChain()
                ->attach(new DefaultFilter('organization'));

            $organizationReference->add($collection);
            $inputFilter->add($organizationReference, 'organizationReference');

            $monitorReference = new InputFilter();

            $id = new Input('id');
            $id->getValidatorChain()
                ->attach(new NotEmpty())
                ->attach(new ObjectIdValidator());
            
            $monitorReference->add($id);

            $parentId = new Input('parentId');
            $parentId->getValidatorChain()
                ->attach(new NotEmpty())
                ->attach(new ObjectIdValidator());
            
            $monitorReference->add($parentId);

            $collection = new Input('collection');
            $collection->setRequired(false);
            $collection->getFilterChain()
                ->attach(new DefaultFilter('monitor'));

            $monitorReference->add($collection);
            $inputFilter->add($monitorReference, 'monitorContainerReference');

            return $inputFilter;
        }
    ])->addDefinitions([
        PlaylistQueryString::class => function(ContainerInterface $c) {
            return new PlaylistQueryString();
        }
    ]);
};
