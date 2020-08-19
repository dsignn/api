<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Monitor\Entity\MonitorReference;
use App\Module\Timeslot\Entity\TimeslotEntity;
use App\Module\Timeslot\Storage\TimeslotStorage;
use App\Module\Timeslot\Storage\TimeslotStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use DI\ContainerBuilder;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        TimeslotStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['timeslot'];

            $hydrator = $c->get('StorageTimeslotEntityHydrator')
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('TimeslotEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('TimeslotEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(Client::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new TimeslotStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('TimeslotEntityPrototype'));


            return $storage;
        }
    ])->addDefinitions([
        'RestTimeslotEntityHydrator' => function(ContainerInterface $c) {

            $timeslotHydrator = new ClassMethodsHydrator();
            $timeslotHydrator->setNamingStrategy(new CamelCaseStrategy());
            $timeslotHydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $timeslotHydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('binds', new HydratorArrayStrategy($timeslotHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('resources', new HydratorArrayStrategy($timeslotHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('monitorContainerReference', new HydratorStrategy($timeslotHydrator, new SingleEntityPrototype(new MonitorReference())));

            return $hydrator;
        }
    ])->addDefinitions([
        'StorageTimeslotEntityHydrator' => function(ContainerInterface $c) {

            $timeslotHydrator = new ClassMethodsHydrator();
            $timeslotHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $timeslotHydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $timeslotHydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('binds', new HydratorArrayStrategy($timeslotHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('resources', new HydratorArrayStrategy($timeslotHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('monitorContainerReference', new HydratorStrategy($timeslotHydrator, new SingleEntityPrototype(new MonitorReference())));
            // TODO split hydrator
            return $hydrator;
        }
    ])->addDefinitions([
        'TimeslotEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new TimeslotEntity());
        }
    ]);
};
