<?php

declare(strict_types=1);

use App\Module\Device\Http\QueryString\DeviceQueryString;
use Graze\ArrayMerger\RecursiveArrayMerger;

/**
 * Monitor settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'device' => [
                        'collection' => 'device'
                    ],
                ],
                'contentNegotiation' => [
                    '/device' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestDeviceEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/device/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestDeviceEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/device' => [
                        'POST' => 'DevicePostValidator'
                    ]
                ],
                'queryString' => [
                    '/device' => [
                        'default' => [
                            'service' => DeviceQueryString::class
                        ]
                    ],
                    '/device/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'service' => DeviceQueryString::class
                        ]
                    ]
                ],
                'authorization' => [
                    '/device/all' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ],
                    '/device/{id:[0-9a-fA-F]{24}}' => [
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
                        ],
                    ],
                    '/device' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                            'privileges' => [
                                [
                                    "method" => "POST",
                                    'allow' => false,
                                ]
                            ]
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
                    ]
                ]
            ],
        ]
    );
};
