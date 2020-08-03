<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\NamingStrategy\CamelCaseStrategy;
use App\Module\Monitor\Entity\MonitorReference;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Module\Restaurant\Storage\Adapeter\Mongo\MenuMongoAdapter;
use App\Module\Restaurant\Storage\MenuStorage;
use App\Module\Restaurant\Storage\MenuStorageInterface;
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
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        MenuStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['menu'];

            $hydrator = $c->get('StorageMenuEntityHydrator')
;
            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('MenuEntityPrototype'));

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setEntityPrototype($c->get('MenuEntityPrototype'));

            $mongoAdapter = new MenuMongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MenuStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('MenuEntityPrototype'));


            return $storage;
        }
    ])->addDefinitions([
        'StorageMenuEntityHydrator' => function(ContainerInterface $c) {

            $menuItemHydrator = new ClassMethodsHydrator();
            $menuItemHydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $menuItemHydrator->addStrategy('_id', new MongoIdStrategy());

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addStrategy('organization', new HydratorStrategy($menuItemHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('items', new HydratorArrayStrategy($menuItemHydrator, new SingleEntityPrototype(new MenuItem())));

            return $hydrator;
        }
    ])->addDefinitions([
        'RestMenuEntityHydrator' => function(ContainerInterface $c) {

            $menuItemHydrator = new ClassMethodsHydrator();
            $menuItemHydrator->setNamingStrategy(new CamelCaseStrategy());
            $menuItemHydrator->addStrategy('_id', new MongoIdStrategy());

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new CamelCaseStrategy());
            $hydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));

            $hydrator->addStrategy('organization', new HydratorStrategy($menuItemHydrator, new SingleEntityPrototype(new Reference())));
            $hydrator->addStrategy('items', new HydratorArrayStrategy($menuItemHydrator, new SingleEntityPrototype(new MonitorReference())));

            return $hydrator;
        }
    ])->addDefinitions([
        'MenuEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new MenuEntity());
        }
    ]);;
};
