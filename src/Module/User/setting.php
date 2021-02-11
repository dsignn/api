<?php
declare(strict_types=1);

use Graze\ArrayMerger\RecursiveArrayMerger;

/**
 * User settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            "settings" => [
                'twig' => [
                    'paths' => [
                       // __DIR__ . '/../src/Module/Restaurant/View/restaurant-menu',
                        realpath(__DIR__ .  '/Mail/Template')
                    ]
                ],
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
                    '/activation-code' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/user' => [
                        'POST' => 'UserPostValidation'
                    ],
                    '/user/{id:[0-9a-fA-F]{24}}' => [
                        'PATCH' => 'UserPatchValidation'
                    ]
                ],
                'authentication' => [
                    '/user' => [
                        'POST' => [
                            'skip' => true
                        ]
                    ]
                ],
                'authorization' => [
                    '/user' => [
                        'admin' => [
                            'allow' => true,
                        //    'assertion' => 'Test',
                            'privileges' => [
                                [
                                    "method" => "GET",
                                    'allow' => true,
                               //     'assertion' => 'Test',
                                ],
                                [
                                    "method" => "POST",
                                    'allow' => false,
                             //       'assertion' => 'Test',
                                ]
                            ]
                        ]
                    ],
                    '/user/{id:[0-9a-fA-F]{24}}' => [
                        'admin' => [
                            'allow' => false,
                            'assertion' => 'Test',
                            'privileges' => [
                                [
                                    "method" => "GET",
                                    'allow' => true,
                                    //     'assertion' => 'Test',
                                ],
                                [
                                    "method" => "POST",
                                    'allow' => false,
                                    //       'assertion' => 'Test',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    );
};