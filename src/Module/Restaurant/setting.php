<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;

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
                        ],
                    ],
                    '/menu/upload-resource' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ],
                    ],
                    '/menu/delete-resource' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ],
                    ]

                ],
                'validation' => [
                    '/menu' => [
                        'POST' => 'MenuValidation'
                    ],
                    '/menu/{id:[0-9a-fA-F]{24}}' => [
                        'PUT' => 'MenuValidation'
                    ],
                    '/menu/upload-resource' => [
                        'POST' => 'ResourceMenuItemValidation'
                    ],
                    '/menu/delete-resource' => [
                        'POST' => 'ResourceMenuItemDeleteValidation'
                    ]
                ]
            ]
        ]
    );
};