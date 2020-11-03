<?php
declare(strict_types=1);

use Graze\ArrayMerger\RecursiveArrayMerger;
use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;

/**
 * Resource settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'resource' => [
                        'collection' => 'resource'
                    ],
                ]
            ]
        ]
    );
};