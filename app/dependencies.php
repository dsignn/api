<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\Accept\AcceptContainer;
use App\Middleware\ContentNegotiation\Accept\JsonAccept;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeContainer;
use App\Middleware\ContentNegotiation\ContentType\JsonContentType;
use DI\ContainerBuilder;
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
    ])
    ->addDefinitions([
        MongoClient::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $mongoSettings = $settings['mongodb'];

            return new MongoClient('mongodb://' . $mongoSettings["host"] . '/');
        }
    ])->addDefinitions([
        ContentTypeContainer::class => function(ContainerInterface $c) {
            $container = new ContentTypeContainer();

            $container->set(
                JsonContentType::class,
                new JsonContentType()
            );

            return $container;
        }
    ])->addDefinitions([
        AcceptContainer::class => function(ContainerInterface $c) {
            $container = new AcceptContainer();

            $container->set(
                JsonAccept::class,
                new JsonAccept()
            );

            return $container;
        }
    ]);
};
