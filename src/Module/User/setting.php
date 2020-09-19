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
                'mail' => [
                    "resetPassword" => "http://127.0.0.1:8081/reset-password",
                    "activationCode" => "http://127.0.0.1:8081/activation-code",
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