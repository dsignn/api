<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'user' => [
                        'collection' => 'user'
                    ]
                ],
                'contentNegotiation' => [
                    '/user' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/user/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/user' => [
                        'POST' => 'UserPostValidation'
                    ]
                ]
            ]
        ]
    );
};