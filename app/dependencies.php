<?php
declare(strict_types=1);

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\ContentNegotiation\Accept\AcceptContainer;
use App\Middleware\ContentNegotiation\Accept\JsonAccept;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeContainer;
use App\Middleware\ContentNegotiation\ContentType\JsonContentType;
use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\User\Storage\UserStorageInterface;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use League\OAuth2\Server\ResourceServer;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client as MongoClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

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

        Twig::class => function(ContainerInterface $container) {

            $settings = $container->get('settings')['twig'];
            $twig = Twig::create($settings['paths'], $settings['options']);

            // TODO Add extension

            return $twig;
        },

        AuthenticationMiddleware::class => function(ContainerInterface $c) {
            return new AuthenticationMiddleware(
                $c->get(ResourceServer::class),
                $c->get(UserStorageInterface::class),
                $c->get('AccessTokenStorage'),
                $c->get('ClientStorage'),
                $c->get('settings')['authentication']
            );
        },

        AuthorizationMiddleware::class => function(ContainerInterface $c) {
            return new AuthorizationMiddleware(
                $c->get('settings')['authorization']
            );
        },

        ValidationMiddleware::class => function(ContainerInterface $c) {
            return new ValidationMiddleware(
                $c->get('settings')['validation'],
                $c
            );
        },

        Client::class => function(ContainerInterface $c) {
            return $client = new \GuzzleHttp\Client();
        },

        "MongoIdStorageStrategy" => function(ContainerInterface $c) {
            return new ClosureStrategy(
                function ($value) {

                    if ($value && is_string($value)) {
                        $value = new ObjectId($value);
                    }
                    return $value;
                },
                function ($value) {
                    if ($value instanceof ObjectId) {
                        $value = $value->__toString();
                    }
                    return $value;
                }
            );
        },

        "MongoIdRestStrategy" => function(ContainerInterface $c) {
            return new ClosureStrategy(
                function ($value) {
                    if ($value instanceof ObjectId) {
                        $value = $value->__toString();
                    }
                    return $value;
                },
                function ($value) {

                    if ($value instanceof ObjectId) {
                        $value = $value->__toString();
                    }
                    return $value;
                }
            );
        },


        "EntityDateRestStrategy" => function(ContainerInterface $c) {
            return new ClosureStrategy(
                function ($value) {

                    if ($value instanceof DateTimeInterface) {
                        $value = $value->format(DateTimeInterface::ATOM);
                    }
                    return $value;
                },
                function ($value) {
                    if (is_string($value)) {
                        // TODO controll
                        $value = DateTime::createFromFormat(DateTimeInterface::ATOM, $value);
                    }
                    if ($value instanceof UTCDateTime) {
                        $value = $value->toDateTime();
                    }
                    return $value;
                }
            );
        },


        "ReferenceMongoHydrator" =>  function(ContainerInterface $c) {
            $hydrator = new ClassMethodsHydrator();
            $hydrator->addStrategy('_id', $c->get('MongoIdStorageStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdStorageStrategy'));
            return $hydrator;
        },

        "ReferenceRestHydrator" =>  function(ContainerInterface $c) {
            $hydrator = new ClassMethodsHydrator();
            $hydrator->addStrategy('_id', $c->get('MongoIdRestStrategy'));
            $hydrator->addStrategy('id', $c->get('MongoIdRestStrategy'));
            return $hydrator;
        }


    ]);
};
