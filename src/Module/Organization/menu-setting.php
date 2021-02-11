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
                    'organization' => [
                        'collection' => 'organization'
                    ]
                ]
            ]
        ]
    );
};