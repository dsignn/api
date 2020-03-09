<?php
declare(strict_types=1);

use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Module\Monitor\Entity\MonitorEntity;
use App\Module\Monitor\Storage\MonitorStorage;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Zend\Hydrator\ClassMethodsHydrator;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MonitorStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['monitor'];

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setObjectPrototype(new MonitorEntity());

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);

            $storage = new MonitorStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setObjectPrototype(new MonitorEntity());

            return $storage;
        }
    ]);
};
