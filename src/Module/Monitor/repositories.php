<?php
declare(strict_types=1);

use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Module\Monitor\Entity\MonitorEntity;
use App\Module\Monitor\Storage\MonitorStorage;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Module\User\Entity\UserEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Zend\Hydrator\ClassMethodsHydrator;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Strategy\ClosureStrategy;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MonitorStorageInterface::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['monitor'];

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setObjectPrototype(new MonitorEntity());

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setObjectPrototype(new MonitorEntity());

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new MonitorStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setObjectPrototype(new MonitorEntity());

            return $storage;
        }
    ])->addDefinitions([
        'RestMonitorEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->addStrategy('id', new ClosureStrategy(function ($data) {

                if ($data instanceof MongoId) {
                    $data = $data->__toString();
                }
                return $data;
            }));

            return $hydrator;
        }
    ]);
};
