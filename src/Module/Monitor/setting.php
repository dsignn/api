<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'monitor' => [
                        'collection' => 'monitor'
                    ],
                ],
                'contentNegotiation' => [
                    '/monitor' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/monitor/{id:[0-9a-fA-F]{24}}' => [
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
            ],
        ]
    );
};