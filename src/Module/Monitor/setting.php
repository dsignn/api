<?php
declare(strict_types=1);

use App\Module\Monitor\Http\QueryString\MonitorQueryString;
use function Rikudou\ArrayMergeRecursive\array_merge_recursive;

/**
 * Monitor settings
 */
return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'monitor' => [
                        'collection' => 'monitor'
                    ],
                ],
                'contentNegotiation' => [
                    '/monitor' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestMonitorEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/monitor/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestMonitorEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/monitor/all' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'acceptFilterHydrator' => 'RestMonitorEntityHydrator',
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/monitor' => [
                        'POST' => 'MonitorPostValidation'
                    ]
                ],
                'queryString' => [
                    '/monitor' => [
                        'default' => [
                            'service' => MonitorQueryString::class
                        ]
                    ],
                    '/monitor/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'service' => MonitorQueryString::class
                        ]
                    ],
                    '/monitor/all' => [
                        'default' => [
                            'service' => MonitorQueryString::class
                        ]
                    ]
                ],
                'authorization' => [
                    '/monitor/all' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ],
                    '/monitor/{id:[0-9a-fA-F]{24}}' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ],
                    '/monitor' => [
                        'admin' => [
                            'allow' => true,
                        ],
                        'organizationOwner' => [
                            'allow' => true,
                        ],
                    ]
                ]
            ],
        ]
    );
};