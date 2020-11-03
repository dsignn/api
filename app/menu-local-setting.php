<?php
declare(strict_types=1);

use Graze\ArrayMerger\RecursiveArrayMerger;
use Monolog\Logger;

/**
 * Local settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            'settings' => [
                'tmp' => '../tmp',
                'determineRouteBeforeAppMiddleware' => true,
                'debug' => true,
                'displayErrorDetails' => true, // Should be set to false in production
                "mongodb" => [
                    "host" => "mongo"
                ],
                'storage' => [
                    'name' => 'dsign'
                ],
                'twig' => [
                    'path-js' => 'http://127.0.0.200/js',
                    'paths' => [
                        __DIR__ . '/../src/Module/Restaurant/View/restaurant-menu',
                        __DIR__ . '/../src/Module/User/Mail/Template'
                    ],
                    'options' => [
                        // Should be set to true in production
                        'cache' => false,
                        'cache_path' => __DIR__ . '/../tmp/twig',
                    ],
                ],
            ],
        ]
    );
};