<?php
declare(strict_types=1);

use App\Filter\DefaultFilter;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\DateStrategy;
use App\Hydrator\Strategy\DefaultStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\InputFilter\Input;
use App\Module\Device\Entity\DeviceEntity;
use App\Module\Device\Storage\DeviceStorage;
use App\Module\Device\Storage\DeviceStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\SingleEntityPrototype;
use DI\ContainerBuilder;
use App\InputFilter\InputFilter as AppInputFilter;
use App\Module\Device\Http\QueryString\DeviceQueryString;
use App\Module\Device\Entity\MonitorContainerEntityReference;
use App\Module\Device\Entity\MonitorEntityReference;
use App\Storage\Entity\Reference;
use App\Validator\Mongo\ObjectIdValidator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\Strategy\DateTimeImmutableFormatterStrategy;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\NotEmpty;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

function monitorRestHydrator(ContainerInterface $c) {

    $playlistHydrator = new ClassMethodsHydrator();
    $playlistHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
    $playlistHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
    $playlistHydrator->addStrategy('collection', new DefaultStrategy('playlist'));

    $monitorHydrator = new ClassMethodsHydrator();
    $monitorHydrator->setNamingStrategy(new CamelCaseStrategy());
    $monitorHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
    $monitorHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
    $monitorHydrator->addStrategy('playlist', new HydratorStrategy($playlistHydrator, new SingleEntityPrototype(new Reference())));
    $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntityReference())));
    $monitorHydrator->addStrategy('collection', new DefaultStrategy('monitor'));
          
    $monitorContainerHydrator = new ClassMethodsHydrator();
    $monitorContainerHydrator->setNamingStrategy(new CamelCaseStrategy());
    $monitorContainerHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
    $monitorContainerHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
    $monitorContainerHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntityReference())));
    $monitorContainerHydrator->addStrategy('collection', new DefaultStrategy('monitor'));

    return $monitorContainerHydrator;
}

function monitorStorageHydrator(ContainerInterface $c) {

    $playlistHydrator = new ClassMethodsHydrator();
    $playlistHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
    $playlistHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
    $playlistHydrator->addStrategy('collection', new DefaultStrategy('playlist'));

    $monitorHydrator = new ClassMethodsHydrator();
    $monitorHydrator->setNamingStrategy(new CamelCaseStrategy());
    $monitorHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
    $monitorHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
    $monitorHydrator->addStrategy('playlist', new HydratorStrategy($playlistHydrator, new SingleEntityPrototype(new Reference())));
    $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntityReference())));
    $monitorHydrator->addStrategy('collection', new DefaultStrategy('monitor'));      

    $monitorContainerHydrator = new ClassMethodsHydrator();
    $monitorContainerHydrator->setNamingStrategy(new CamelCaseStrategy());
    $monitorContainerHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
    $monitorContainerHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
    $monitorContainerHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntityReference())));
    $monitorContainerHydrator->addStrategy('collection', new DefaultStrategy('monitor'));      


    return $monitorContainerHydrator;
}

function monitorContainerInputFilter(ContainerInterface $c) {

    $inputFilterMonitorContainer = new AppInputFilter();
   
    $id = new Input('id');
    $id->setRequired(false);
    $id->getValidatorChain()
        ->attach(new ObjectIdValidator());

    $monitorCollection = new Input('collection');
    $monitorCollection->setRequired(false);
    $monitorCollection->getFilterChain()
        ->attach(new DefaultFilter('monitor'));  
     
    $collectionMonitorInputFilter = new CollectionInputFilter();
    $collectionMonitorInputFilter->setIsRequired(false);
    $collectionMonitorInputFilter->setInputFilter(monitorInputFilter($c));

    $inputFilterMonitorContainer
        ->add($id)
        ->add($monitorCollection)
        ->add($collectionMonitorInputFilter, 'monitors')
        ;
       
    return $inputFilterMonitorContainer;
}

function playlistInputFilter(ContainerInterface $c) {

    $id = new Input('id');
    $id->setRequired(false);
    $id->getValidatorChain()
        ->attach(new ObjectIdValidator());

    $playlistCollection = new Input('collection');
    $playlistCollection->setRequired(false);
    $playlistCollection->getFilterChain()
        ->attach(new DefaultFilter('playlist'));    

    $inputFilter = new AppInputFilter();
    $inputFilter->add($id)
        ->add($playlistCollection);

    return $inputFilter;
}

function monitorInputFilter(ContainerInterface $c) {

    $id = new Input('id');
    $id->setRequired(false);
    $id->getValidatorChain()
        ->attach(new ObjectIdValidator()); 

    $inputFilter = new AppInputFilter();
    $inputFilter->add($id)
        ->add(playlistInputFilter($c), 'playlist');

    // TODO create recursive input filter 

    return $inputFilter;
}

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        DeviceStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['device'];

            $hydrator = $c->get('StorageDeviceEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('DeviceEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new DeviceStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('DeviceEntityPrototype'));

            return $storage;
        }
    ])->addDefinitions([
        'DeviceEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new DeviceEntity());
        }
    ])
    ->addDefinitions([
        'RestDeviceEntityHydrator' => function(ContainerInterface $c) {

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $hydrator = new ObjectPropertyHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('lastUpdateDate', new DateStrategy());
            $hydrator->addStrategy('createdDate', new DateStrategy());
            $hydrator->addStrategy('monitor', new HydratorStrategy(monitorRestHydrator($c), new SingleEntityPrototype(new MonitorContainerEntityReference())));

            return $hydrator;
        }
    ])->addDefinitions([
        'StorageDeviceEntityHydrator' => function(ContainerInterface $c) {

            $organizationHydrator = new ClassMethodsHydrator();
            $organizationHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $organizationHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $hydrator = new ObjectPropertyHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('organizationReference', new HydratorStrategy($organizationHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('lastUpdateDate', new MongoDateStrategy());
            $hydrator->addStrategy('createdDate', new MongoDateStrategy());
            $hydrator->addStrategy('monitor', new HydratorStrategy(monitorStorageHydrator($c), new SingleEntityPrototype(new MonitorContainerEntityReference())));
           
            return $hydrator;
        }
    ])
    ->addDefinitions([
        'DevicePostValidator' => function(ContainerInterface $c) {

       
            $inputFilter = new AppInputFilter();

            $id = new Input('id');
            $id->getValidatorChain()->attach(new NotEmpty());

            $inputFilter->add($id)
                ->add(monitorContainerInputFilter($c), 'monitor');

            return $inputFilter;
        }
    ])->addDefinitions([
        DeviceQueryString::class => function(ContainerInterface $c) {
            return new DeviceQueryString();
        }
    ]);
    
};
