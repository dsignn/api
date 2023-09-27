<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;
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
                'contentNegotiation' => [
                    '/resource' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestResourceEntityHydrator',
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ]
                    ],
                    '/resource/all' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestResourceEntityHydrator',
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ]
                    ],
                    '/resource/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestResourceEntityHydrator',
                            'contentTypeFilter' => ['/multipart\/form-data/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ],
                        'PUT' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestResourceEntityHydrator',
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
                'authorization' => [
                    '/resource' => [
                        'organizationOwner' => [
                            'allow' => true,
                            'privileges' => [
                                [
                                    "method" => "GET",
                                    'allow' => true,
                                ],
                                [
                                    "method" => "POST",
                                    'allow' => true,
                                ]
                            ]
                        ]
                    ],
                    '/resource/{id:[0-9a-fA-F]{24}}' => [
                        'organizationOwner' => [
                            'allow' => false,
                            'privileges' => [
                                [
                                    "method" => "GET",
                                    'allow' => true,
                                    //     'assertion' => 'Test',
                                ],
                                [
                                    "method" => "POST",
                                    'allow' => false,
                                    //       'assertion' => 'Test',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    );
};