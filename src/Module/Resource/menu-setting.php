<?php
declare(strict_types=1);

use Graze\ArrayMerger\RecursiveArrayMerger;

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
                ],
                'ffmpeg' => [
                    'binary' => [
                        'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                        'ffprobe.binaries'  => '/usr/bin/ffprobe',
                    ]
                ]
            ],
        ]
    );
};