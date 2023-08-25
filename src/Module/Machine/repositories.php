<?php
declare(strict_types=1);

use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Machine\Entity\MachineEntity;
use App\Module\Machine\Storage\MachineStorage;
use App\Module\Machine\Storage\MachineStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\SingleEntityPrototype;
use DI\ContainerBuilder;
use Laminas\Hydrator\ClassMethodsHydrator;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MachineStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['machine'];

            $hydrator = $c->get('StorageMonitorEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('MachineEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('MonitorEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MachineStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('MonitorEntityPrototype'));

            return $storage;
        }
    ])->addDefinitions([
        'MachineEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new MachineEntity());
        }
    ])
    /*
    ->addDefinitions([
        'RestMonitorEntityHydrator' => function(ContainerInterface $c) {

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->setNamingStrategy(new CamelCaseStrategy());
            $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            return $hydrator;
        }
    ])->addDefinitions([
        'StorageMonitorEntityHydrator' => function(ContainerInterface $c) {

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $hydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            return $hydrator;
        }
    ])
    */;
};
