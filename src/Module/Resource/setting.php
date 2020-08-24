<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\ContentType\JsonContentType;
use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'resource' => [
                        'collection' => 'resource'
                    ],
                ],
                'contentNegotiation' => [
                    '/resource' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ]
                    ],
                    '/resource/all' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ]
                    ],
                    '/resource/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ],
                        'PUT' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ]
                    ],
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
                'ffmpeg' => [
                    'binary' => [
                        'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                        'ffprobe.binaries'  => '/usr/bin/ffprobe',
                    ]
                ],
                'validation' => [
                    '/resource' => [
                        'POST' => 'ResourcePostValidator'
                    ],
                    '/resource/{id:[0-9a-fA-F]{24}}' => [
                        'PATCH' => 'ResourceValidator'
                    ]
                ],
            ]
        ]
    );
};