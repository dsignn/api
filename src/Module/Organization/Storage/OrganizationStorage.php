<?php
declare(strict_types=1);

namespace App\Module\Organization\Storage;

use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Storage;
use MongoDB\Driver\Cursor;

/**
 * Class OrganizationStorage
 * @package App\Module\Organization\Storage
 */
class OrganizationStorage extends Storage implements OrganizationStorageInterface {

    /**
     * @param string $slug
     * @return \App\Storage\Entity\EntityInterface|mixed|object|null
     */
    public function getMenuBySlug(string $slug) {

        if ($this->storage instanceof MongoAdapter) {
            var_dump(__FUNCTION__);
            $pipeline = [
                [
                    '$match' =>
                        ['normalize_name' => $slug],
                ],
                [
                    '$lookup' => [
                        'from' => 'menu',
                        'localField' => 'id',
                        'foreignField' => 'organization.id',
                        'as' => 'menu'
                    ]
                ],
                [
                    '$unwind' => '$menu'
                ],
                [
                    '$match' =>
                        ['menu.enable' => true],
                ],
            ];

            /** @var Cursor $aggregation */
            $aggregation = $this->storage->getCollection()->aggregate($pipeline);
            $l = $aggregation->toArray();
            var_dump( count($l));
            var_dump( $l[0]);


        }


        var_dump($slug);
    }
}