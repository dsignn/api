<?php
declare(strict_types=1);

use Graze\ArrayMerger\RecursiveArrayMerger;

/**
 * Organization settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'order' => [
                        'collection' => 'order'
                    ]
                ],
                'contentNegotiation' => [
                    '/order' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/order/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ]
                ],
                'validation' => [ 
                    '/order' => [
                        'POST' => 'OrderValidation'
                    ],
                    '/order/{id:[0-9a-fA-F]{24}}' => [
                        'PUT' => 'OrderValidation'
                    ]
                ],
                'order-cors' => 'http://127.0.0.160:8081'
            ]
        ]
    );
};