<?php
declare(strict_types=1);

//use Graze\ArrayMerger\RecursiveArrayMerger;

/**
 * Organization settings
 */
return function (&$setting) {

    //$merger = new RecursiveArrayMerger();
    $setting = array_merge_recursive(
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