<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\ContentType\MultipartFormDataContentType;

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'resource' => [
                        'collection' => 'monitor'
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
                    '/resource/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/multipart\/form-data/'],
                            'contentTypeFilter' => ['/application\/json/'],
                            'contentTypeService' => MultipartFormDataContentType::class
                        ]
                    ],
                ],
            ]
        ]
    );
};