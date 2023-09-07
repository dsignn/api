<?php
declare(strict_types=1);

use App\Module\Monitor\Http\QueryString\MonitorQueryString;
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
                    '/monitor' => [
                        'POST' => 'MonitorPostValidation'
                    ]
                ],
                'queryString' => [
                    '/monitor' => [
                        'default' => [
                            'service' => MonitorQueryString::class
                        ]
                    ],
                    '/monitor/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'service' => MonitorQueryString::class
                        ]
                    ]
                ]
            ],
        ]
    );
};