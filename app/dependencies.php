<?php
declare(strict_types=1);

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\ContentNegotiation\Accept\AcceptContainer;
use App\Middleware\ContentNegotiation\Accept\JsonAccept;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeContainer;
use App\Middleware\ContentNegotiation\ContentType\JsonContentType;
use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\User\Storage\UserStorageInterface;
use DI\ContainerBuilder;
use League\OAuth2\Server\ResourceServer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        MongoClient::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $mongoSettings = $settings['mongodb'];

            return new MongoClient('mongodb://' . $mongoSettings["host"] . '/');
        },

        ContentTypeContainer::class => function(ContainerInterface $c) {
            $container = new ContentTypeContainer();

            $container->set(
                JsonContentType::class,
                new JsonContentType()
            );

            $container->set(
                MultipartFormDataContentType::class,
                new MultipartFormDataContentType()
            );

            return $container;
        },

        AcceptContainer::class => function(ContainerInterface $c) {
            $container = new AcceptContainer();

            $container->set(
                JsonAccept::class,
                new JsonAccept()
            );

            return $container;
        },

        AuthenticationMiddleware::class => function(ContainerInterface $c) {
            return new AuthenticationMiddleware(
                $c->get(ResourceServer::class),
                $c->get(UserStorageInterface::class),
                $c->get('AccessTokenStorage'),
                $c->get('settings')['authentication']
            );
        },

        ValidationMiddleware::class => function(ContainerInterface $c) {
            return new ValidationMiddleware(
                $c->get('settings')['validation'],
                $c
            );
        }
    ]);
};
