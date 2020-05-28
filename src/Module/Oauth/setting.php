<?php
declare(strict_types=1);

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'contentNegotiation' => [
                    '/me' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'oauth' => [
                    'encryption-key' => 'h1Z6HajxU9ObuJKotafqqxriGuuuRhqSd1VZK7wAnXU=',
                    'path-private-key' => __DIR__ . '/../../../key/dsign-oauth-private.key',
                    'path-public-key' => __DIR__ . '/../../../key/dsign-oauth-public.key',
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
        ]
    );
};