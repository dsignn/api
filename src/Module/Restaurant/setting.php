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
                    ],
                    'menu-category' => [
                        'collection' => 'menu-category'
                    ]
                ],
                'contentNegotiation' => [
                    '/menu' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/menu/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/menu-category' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/menu' => [
                        'POST' => 'MenuValidation'
                    ]
                ],
            ]
        ]
    );
};