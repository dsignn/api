<?php
declare(strict_types=1);

use App\Crypto\CryptoOpenSsl;
use App\Crypto\DefuseCrypto;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Hydrator\Strategy\Mongo\MongoDateStrategy;
use App\Hydrator\Strategy\Mongo\MongoIdStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\MongoUnderscoreNamingStrategy;
use App\Hydrator\Strategy\Mongo\NamingStrategy\UnderscoreNamingStrategy;
use App\Module\Oauth\Entity\AccessTokenEntity;
use App\Module\Oauth\Entity\ClientEntity;
use App\Module\Oauth\Repository\AccessTokenRepository;
use App\Module\Oauth\Repository\AuthCodeRepository;
use App\Module\Oauth\Repository\ClientRepository;
use App\Module\Oauth\Repository\RefreshTokenRepository;
use App\Module\Oauth\Repository\ScopeRepository;
use App\Module\Oauth\Repository\UserRepository;
use App\Module\User\Entity\UserEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Storage;
use Defuse\Crypto\Key;
use DI\ContainerBuilder;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

return function (ContainerBuilder $containerBuilder) {


};
