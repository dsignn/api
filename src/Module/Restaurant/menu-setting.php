<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;
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
                    ]
                ],
            ]
        ]
    );
};