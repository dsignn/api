<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Crypto\DefuseCrypto;
use App\Hydrator\Strategy\HydratorArrayStrategy;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Module\Oauth\Entity\AccessTokenEntity;
use App\Module\Oauth\Entity\AuthCodeEntity;
use App\Module\Oauth\Entity\ClientEntity;
use App\Module\Oauth\Entity\RefreshTokenEntity;
use App\Module\Oauth\Entity\ScopeEntity;
use App\Module\Oauth\Repository\AccessTokenRepository;
use App\Module\Oauth\Repository\AuthCodeRepository;
use App\Module\Oauth\Repository\ClientRepository;
use App\Module\Oauth\Repository\RefreshTokenRepository;
use App\Module\Oauth\Repository\ScopeRepository;
use App\Module\Oauth\Repository\UserRepository;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\Storage;
use Defuse\Crypto\Key;
use DI\ContainerBuilder;
use Laminas\Hydrator\ClassMethodsHydrator;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([

        'OAuthCrypto' => function(ContainerInterface $c) {

            $key = null;
            if (file_exists(__DIR__ . '/../../../key/dsign-oauth-password.txt')) {
                $key = Key::loadFromAsciiSafeString(file_get_contents(__DIR__ . '/../../../key/dsign-oauth-password.txt'));
            }
            return new DefuseCrypto($key);
        },

        'ClientStorage' => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['oauth']['client']['storage'];

            $hydrator = $c->get('StorageClientEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('ClientEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $serviceSetting['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);

            $storage = new Storage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('ClientEntityPrototype'));

            return $storage;
        },

        'ClientEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new ClientEntity());
        },

        'StorageClientEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());

            return $hydrator;
        },

        ClientRepository::class => function(ContainerInterface $c) {
            return new ClientRepository($c->get('ClientStorage'), $c->get('OAuthCrypto'));
        },

        'AccessTokenStorage' => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['oauth']['access-token']['storage'];

            $hydrator = $c->get('StorageAccessTokenEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('AccessTokenEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $serviceSetting['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);

            $storage = new Storage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('AccessTokenEntityPrototype'));

            return $storage;
        },

        'AccessTokenEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new AccessTokenEntity());
        },

        'StorageAccessTokenEntityHydrator' => function(ContainerInterface $c) {

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addStrategy('client', new HydratorStrategy(  new ClassMethodsHydrator(), new ClientEntity()));
            $hydrator->addStrategy('expiry_date_time', new MongoDateStrategy(new DateTimeImmutable()));
            $hydrator->addStrategy('scopes', new HydratorArrayStrategy(  new ClassMethodsHydrator(), new SingleEntityPrototype(new ScopeEntity())));

            return $hydrator;
        },

        AccessTokenRepository::class => function(ContainerInterface $c) {
            return new AccessTokenRepository($c->get('AccessTokenStorage'));
        },

        'UserStorage' => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['oauth']['user']['storage'];

            $hydrator = $c->get('StorageUserEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('UserEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $serviceSetting['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);

            $storage = new Storage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('UserEntityPrototype'));

            return $storage;
        },

        UserRepository::class => function(ContainerInterface $c) {
            return new UserRepository($c->get('UserStorage'),  $c->get('OAuthCrypto'));
        },

        'RefreshTokenStorage' =>  function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['oauth']['refresh-token']['storage'];

            $hydrator = $c->get('StorageRefreshTokenEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('RefreshTokenEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $serviceSetting['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);

            $storage = new Storage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('RefreshTokenEntityPrototype'));

            return $storage;
        },

        'StorageRefreshTokenEntityHydrator' => function(ContainerInterface $c) {

            $accessTokenHydrator = new ClassMethodsHydrator();
            $accessTokenHydrator->addStrategy('client', new HydratorStrategy(new ClassMethodsHydrator(), new SingleEntityPrototype(new ClientEntity())));
            $accessTokenHydrator->addStrategy('scopes', new HydratorArrayStrategy(  new ClassMethodsHydrator(), new SingleEntityPrototype(new ScopeEntity())));
            $accessTokenHydrator->addStrategy('expiry_date_time', new MongoDateStrategy(new DateTimeImmutable()));

            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addStrategy('expiry_date_time', new MongoDateStrategy(new DateTimeImmutable()));
            $hydrator->addStrategy('accessToken', new HydratorStrategy($accessTokenHydrator, new AccessTokenEntity()));

            return $hydrator;
        },

        'RefreshTokenEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new RefreshTokenEntity());
        },

        RefreshTokenRepository::class => function(ContainerInterface $c) {
            return new RefreshTokenRepository($c->get('RefreshTokenStorage'));
        },

        'AuthCodeStorage' => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $serviceSetting = $settings['oauth']['auth-code']['storage'];

            $hydrator = $c->get('StorageAuthCodeEntityHydrator');

            $resultSet = new MongoHydrateResultSet();
            $resultSet->setHydrator($hydrator);
            $resultSet->setEntityPrototype($c->get('AuthCodeEntityPrototype'));

            $mongoAdapter = new MongoAdapter($c->get(MongoClient::class), $serviceSetting['name'], $serviceSetting['collection']);
            $mongoAdapter->setResultSet($resultSet);

            $storage = new Storage($mongoAdapter);
            $storage->setHydrator($hydrator);
            $storage->setEntityPrototype($c->get('AuthCodeEntityPrototype'));
            return $storage;
        },

        'AuthCodeEntityPrototype' => function(ContainerInterface $c) {
            return new SingleEntityPrototype(new AuthCodeEntity());
        },

        'StorageAuthCodeEntityHydrator' => function(ContainerInterface $c) {
            $hydrator = new ClassMethodsHydrator();
            $hydrator->setNamingStrategy(new MongoUnderscoreNamingStrategy());
            $hydrator->addStrategy('id', new MongoIdStrategy());
            $hydrator->addStrategy('client', new HydratorStrategy(new ClassMethodsHydrator(), new ClientEntity()));

            return $hydrator;
        },

        AuthCodeRepository::class => function(ContainerInterface $c) {

            return new AuthCodeRepository($c->get('AuthCodeStorage'));
        },

        ScopeRepository::class => function(ContainerInterface $c) {

            return new ScopeRepository();
        },

        AuthorizationServer::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $oauthSettings =  $settings['oauth'];

            $server = new AuthorizationServer(
                $c->get(ClientRepository::class),
                $c->get(AccessTokenRepository::class),
                $c->get(ScopeRepository::class),
                $oauthSettings['path-private-key'],
                $oauthSettings['encryption-key']
            );

            $server->enableGrantType(
                new ClientCredentialsGrant(),
                new DateInterval('P1Y') // access tokens will expire after 1 hour
            );

            /**
             * Password grant
             */
            $grant = new PasswordGrant(
                $c->get(UserRepository::class),
                $c->get(RefreshTokenRepository::class)
            );

            $grant->setRefreshTokenTTL(new DateInterval('P1Y')); // refresh tokens will expire after 1 month
            $server->enableGrantType($grant);

            /**
             * Refresh token grant
             */
            $grant = new RefreshTokenGrant($c->get(RefreshTokenRepository::class));
            $grant->setRefreshTokenTTL(new DateInterval('P1Y'));

            $server->enableGrantType(
                $grant,
                new DateInterval('P1Y') // new access tokens will expire after an hour
            );

            /**
             * Auth code token grant
             */
            $grant = new AuthCodeGrant(
                $c->get(AuthCodeRepository::class),
                $c->get(RefreshTokenRepository::class),
                new DateInterval('P1Y') // authorization codes will expire after 10 minutes
            );

            $grant->setRefreshTokenTTL(new DateInterval('P1Y'));

            $server->enableGrantType(
                $grant,
                new DateInterval('P1Y') // new access tokens will expire after an hour
            );

            /**
             * Implicit token grant
             */
            $server->enableGrantType(
                new ImplicitGrant(new \DateInterval('P1Y')),
                new DateInterval('P1Y') // access tokens will expire after 1 hour
            );

            return $server;
        },

        ResourceServer::class => function(ContainerInterface $c) {

            $settings = $c->get('settings');
            $oauthSettings =  $settings['oauth'];

            return new ResourceServer(
                $c->get(AccessTokenRepository::class),
                $oauthSettings['path-public-key']
            );
        }
    ]);
};
