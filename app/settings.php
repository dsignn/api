<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            "mongodb" => [
                "host" => "mongo"
            ],
            'storage' => [
                'name' => 'dsign',
                'monitor' => [
                    'collection' => 'monitor'
                ],
                'user' => [
                    'collection' => 'user'
                ]
            ],
            'oauth' => [
                'encryption-key' => 'h1Z6HajxU9ObuJKotafqqxriGuuuRhqSd1VZK7wAnXU=',
                'path-private-key' => __DIR__ . '/../key/dsign-oauth-private.key',
                'client' => [
                    'storage' => [
                        'type' => 'mongo',
                        'name' => 'dsign-oauth',
                        'collection' => 'client'
                    ]
                ],
                'access-token' => [
                    'storage' => [
                        'type' => 'mongo',
                        'name' => 'dsign-oauth',
                        'collection' => 'access-token'
                    ]
                ],
                'user' => [
                    'storage' => [
                        'type' => 'mongo',
                        'name' => 'dsign',
                        'collection' => 'user'
                    ]
                ],
                'auth-code' => [
                    'storage' => [
                        'type' => 'mongo',
                        'name' => 'dsign-oauth',
                        'collection' => 'auth-code'
                    ]
                ],
                'refresh-token' => [
                    'storage' => [
                        'type' => 'mongo',
                        'name' => 'dsign-oauth',
                        'collection' => 'refresh-token'
                    ]
                ],
            ]
        ],
    ]);
};
