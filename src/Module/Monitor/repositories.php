<?php
declare(strict_types=1);

use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Monitor\Entity\MonitorContainerEntity;
use App\Module\Monitor\Entity\MonitorEntity;
use App\Module\Monitor\Storage\MonitorStorage;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\SingleEntityPrototype;
use DI\ContainerBuilder;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MonitorStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['monitor'];

            $hydrator = $c->get('StorageMonitorEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('MonitorEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('MonitorEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MonitorStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('MonitorEntityPrototype'));

            return $storage;
        }
    ])->addDefinitions([
        'MonitorEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new MonitorContainerEntity());
        }
    ])->addDefinitions([
        'RestMonitorEntityHydrator' => function(ContainerInterface $c) {

            $monitorHydrator = new ClassMethodsHydrator();
            $monitorHydrator->setNamingStrategy(new CamelCaseStrategy());
            $monitorHydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));
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
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addStrategy('monitors', new HydratorArrayStrategy($monitorHydrator, new SingleEntityPrototype(new MonitorEntity())));

            return $hydrator;
        }
    ]);
};
