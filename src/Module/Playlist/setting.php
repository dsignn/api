<?php
declare(strict_types=1);

use App\Module\Playlist\Http\QueryString\PlaylistQueryString;
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
                    ],
                    '/playlist/all' => [
                        'default' => [
                                'acceptFilterHydrator' => 'RestPlaylistEntityHydrator',
                                'acceptFilter' => ['/application\/json/'],
                                'contentTypeFilter' => ['/application\/json/']
                            ]
                     ],
                ],
                'validation' => [
                    '/playlist' => [
                        'POST' => 'PlaylistPostValidation'
                    ],
                    '/playlist/{id:[0-9a-fA-F]{24}}' => [
                        'PUT' => 'PlaylistPostValidation'
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
                ],
                'queryString' => [
                    '/playlist' => [
                        'default' => [
                            'service' => PlaylistQueryString::class
                        ]
                    ],
                    '/playlist/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'service' => PlaylistQueryString::class
                        ]
                    ],
                    '/playlist/all' => [
                        'default' => [
                            'service' => PlaylistQueryString::class
                        ]
                    ]             
                ],
            ]
        ]
    );
};