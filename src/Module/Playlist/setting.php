<?php
declare(strict_types=1);

use Graze\ArrayMerger\RecursiveArrayMerger;

/**
 * User settings
 */
return function (&$setting) {

    $merger = new RecursiveArrayMerger();
    $setting = $merger->merge(
        $setting,
        [
            "settings" => [

                'storage' => [
                    'playlist' => [
                        'collection' => 'playlist'
                    ]
                ],
                'contentNegotiation' => [
                    '/playlist' => [
                       'default' => [
                            'acceptFilterHydrator' => 'RestPlaylistEntityHydrator',
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/playlist/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestPlaylistEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ]
                ],
                'validation' => [
                    '/playlist' => [
                        'POST' => 'PlaylistPostValidation'
                    ]
                ],
                'authorization' => [
                    '/playlist/all' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ],
                    '/playlist/{id:[0-9a-fA-F]{24}}' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ],
                    '/playlist' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ]
                ]
            ]
        ]
    );
};