<?php
declare(strict_types=1);

/**
 * Timeslot settings
 */
return function (&$setting) {

    $setting = array_merge_recursive(
        $setting,
        [
            "settings" => [
                'storage' => [
                    'timeslot' => [
                        'collection' => 'timeslot'
                    ]
                ],
                'contentNegotiation' => [
                    '/timeslot' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                    '/timeslot/{id:[0-9a-fA-F]{24}}' => [
                        'default' => [
                            'acceptFilter' => ['/application\/json/'],
                            'contentTypeFilter' => ['/application\/json/']
                        ]
                    ],
                ],
                'validation' => [
                    '/timeslot' => [

                    ]
                ]
            ]
        ]
    );
};