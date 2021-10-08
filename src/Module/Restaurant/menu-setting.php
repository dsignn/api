<?php
declare(strict_types=1);

use App\Module\Restaurant\Middleware\Accept\MenuAccept;
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
                ],
                'twig' => [
                    'paths' => [
                        realpath(__DIR__ . '/View/restaurant-menu'),
                        realpath(__DIR__ . '/View/print-qrcode'),
                        realpath(__DIR__ . '/View/print-menu'),
                        realpath(__DIR__ . '/View'),
                    ]
                ],
                'contentNegotiation' => [
                    '/{slug}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/', '/text\/html/'],
                            'contentTypeFilter' => ['/application\/json/'],
                            'acceptService' => MenuAccept::class
                        ]
                    ]
                ],
            ]
        ]
    );
};