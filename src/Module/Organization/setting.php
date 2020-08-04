<?php
declare(strict_types=1);

return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'organization' => [
                        'collection' => 'organization'
                    ]
                ],
                'contentNegotiation' => [
                    '/organization' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/organization/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/generate-qrcode/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/organization' => [
                        'POST' => 'PostOrganizationValidator'
                    ],
                    '/organization/{id:[0-9a-fA-F]{24}}' => [
                        'PUT' => 'PutOrganizationValidator'
                    ]
                ],
            ]
        ]
    );
};