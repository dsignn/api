<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;
use Graze\ArrayMerger\RecursiveArrayMerger;

/**
 * Restaurant settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'menu' => [
                        'collection' => 'menu'
                    ],
                    'menu-category' => [
                        'collection' => 'menu-category'
                    ],
                    'menu-allergens' => [
                        'collection' => 'menu-allergens'
                    ],
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
                    '/menu-allergen' => [
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