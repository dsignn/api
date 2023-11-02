<?php
declare(strict_types=1);

use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\DateStrategy;
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
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\Strategy\DateTimeImmutableFormatterStrategy;
use Laminas\Validator\NotEmpty;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

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
        
            $hydrator = new ObjectPropertyHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('organizationReference', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('lastUpdateDate', new DateStrategy());
            $hydrator->addStrategy('createdDate', new DateStrategy());
           
            return $hydrator;
        }
    ])->addDefinitions([
        'StorageDeviceEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ObjectPropertyHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            
            $hydrator->addStrategy('last_update_date', new MongoDateStrategy());
            $hydrator->addStrategy('lastUpdateDate', new MongoDateStrategy());
            $hydrator->addStrategy('createdDate', new MongoDateStrategy());
            $hydrator->addStrategy('organizationReference' ,$c->get('MongoIdStorageStrategy'));
           
            return $hydrator;
        }
    ])
    ->addDefinitions([
        'DevicePostValidator' => function(ContainerInterface $c) {

       
            $inputFilter = new AppInputFilter();

            $id = new Input('id');
            $id->getValidatorChain()->attach(new NotEmpty());

            $inputFilter->add($id);

            return $inputFilter;
        }
    ])->addDefinitions([
        DeviceQueryString::class => function(ContainerInterface $c) {
            return new DeviceQueryString();
        }
    ]);
    
};
