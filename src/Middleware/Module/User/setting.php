<?php
declare(strict_types=1);

use App\Module\Resource\Http\QueryString\ResourceQueryString;
use App\Module\User\Http\QueryString\UserQueryString;
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
                            'acceptFilterHydrator' => 'RestUserEntityHydrator',
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/user/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestUserEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/user/all' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestUserEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/activation-code' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestUserEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/recover-password' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RpcPasswordUserEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    
                    '/reset-password' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestUserEntityHydrator',
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
                            'public' => true
                        ]
                    ]
                ],
                'authorization' => [
                    '/user' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                        'guest' => [
                            'allow' => false,
                            'privileges' => [
                                [
                                    "method" => "POST",
                                    'allow' => true,
                                ]
                            ]
                        ],
                    ],
                    '/user/{id:[0-9a-fA-F]{24}}' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                            'privileges' => [
                                [
                                    "method" => "DELETE",
                                    'allow' => false,
                                ]
                            ]
                        ]
                    ]
                ],
                'queryString' => [
                    '/user' => [
                        'default' => [
                            'service' => UserQueryString::class
                        ]
                    ],
                    '/user/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'service' => UserQueryString::class
                        ]
                    ]
                ],
            ]
        ]
    );
};