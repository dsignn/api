<?php
declare(strict_types=1);

namespace App\Module\Organization\Storage;

use App\Module\Restaurant\Entity\MenuEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Adapter\Mongo\ResultSet\MongoHydrateResultSet;
use App\Storage\Storage;

/**
 * Class OrganizationStorage
 * @package App\Module\Organization\Storage
 */
class OrganizationStorage extends Storage implements OrganizationStorageInterface {

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getRandomRestaurantMenu(): array {

        $organizations = new MongoHydrateResultSet();
        if ($this->storage instanceof MongoAdapter) {
            
            $pipeline = [
                [
                    '$lookup' => [
                        'from' => 'menu',
                        'localField' => '_id',
                        'foreignField' => 'organization.id',
                        'as' => 'menus'
                    ]
                ],
                [
                    '$match' =>
                        ['menus' => [
                            '$elemMatch' => [
                                "status" => [
                                    '$in' => [MenuEntity::$STATUS_ENABLE, MenuEntity::$STATUS_DELIVERY]
                                ]
                            ]
                        ]
                    ],
                ],
                [
                    '$project' => [
                        'name' => true,
                        'normalize_name' => true,
                        'qr_code' => true,
                        'qr_code_delivery' => true,
                        'logo' => true,
                        'whatsapp_phone' => true,
                        'site_url' => true,
                        'menus' => [
                            '$filter' => [
                                'input' => '$menus',
                                'as' => 'menu',
                                'cond' => [
                                    '$in' => [
                                        '$$menu.status', [MenuEntity::$STATUS_DELIVERY, MenuEntity::$STATUS_ENABLE]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$sample' => [
                        'size' => 12
                    ]
                ],
                [
                    '$lookup' => [
                        'from' => 'resource',
                        'localField' => 'logo.id',
                        'foreignField' => '_id',
                        'as' => 'logo'
                    ]
                ],
                [
                    '$unwind' => [
                        'path' => '$logo',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ]
            ];

            $cursor = $this->storage->getCollection('organization')->aggregate(
                $pipeline,
                ['typeMap' => ['root' => 'array', 'array' => 'array', 'array' => 'array']]
            );

            $organizations = $cursor->toArray();
        }
      
        return $organizations;
    }
}