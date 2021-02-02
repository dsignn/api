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
                ],
                's3Resource' => [
                    'client' => [
                        'version' => 'latest',
                        'region'  => 'eu-central-1',
                        //      'debug'   => true,
                        'credentials' => [
                            'key' => 'AKIAJUETFABXHRWGNGGQ',
                            'secret' => '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'
                        ]
                    ],
                    'bucket' => 'dsign-cdn-test'
                ],
            ],
        ]
    );
};