<?php
declare(strict_types=1);

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'menu' => [
                        'collection' => 'menu'
                    ]
                ],
                'contentNegotiation' => [
                    '/restaurant/{id:[0-9a-fA-F]{24}}/menu' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/restaurant/{id:[0-9a-fA-F]{24}}/menu/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ]
            ]
        ]
    );
};