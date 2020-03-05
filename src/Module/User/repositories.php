<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Module\Oauth\Entity\ClientEntity;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Storage\UserStorage;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydratePaginateResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Storage;
use DI\ContainerBuilder;
use MongoDB\Client;
use Psr\Container\ContainerInterface;
use Zend\Hydrator\ClassMethodsHydrator;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        UserStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $serviceSetting = $settings['storage']['user'];

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setObjectPrototype(new UserEntity());

            $resultSetPaginator = new MongoHydratePaginateResultSet();
            $resultSetPaginator->setHydrator($hydrator);
            $resultSetPaginator->setObjectPrototype(new UserEntity());

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $settings['storage']['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);
            $mongoAdapter->setResultSetPaginate($resultSetPaginator);

            $storage = new UserStorage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setObjectPrototype(new UserEntity());

            return $storage;
        }
    ]);
};
