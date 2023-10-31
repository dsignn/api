<?php
declare(strict_types=1);

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
                'authorization' => [
                    '/device/all' => [
                        'admin' => [
                            'allow' => true,
                        ]
                    ],
                    '/device/{id:[0-9a-fA-F]{24}}' => [
                        'admin' => [
                            'allow' => true,
                        ]
                    ],
                    '/device' => [
                        'admin' => [
                            'allow' => true,
                        ]
                    ]
                ]
            ],
        ]
    );
};