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
                ],
                'contentNegotiation' => [
                    '/organization' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestOrganizationEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/organization/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestOrganizationEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/organization/upload-resource' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/']
                        ]
                    ],
                    '/organization/all' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestOrganizationEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
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
                'authentication' => [
                    '/organization/all' => [
                        'GET' => [
                            'public' => true
                        ]
                    ]
                ],
                'authorization' => [
                    '/organization/all' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                        'guest' => [
                            'allow' => true,
                            'privileges' => [
                                [
                                    "method" => "GET",
                                    'allow' => true,
                                ]
                            ]
                        ]
                    ],
                    '/organization/{id:[0-9a-fA-F]{24}}' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                            'privileges' => [
                                [
                                    "method" => "DELETE",
                                    'allow' => false,
                                ]
                            ]
                        ]
                    ],
                    '/organization' => [
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