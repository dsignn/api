<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Storage;

use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Storage;
use MongoDB\Driver\Cursor;
use function DI\value;

/**
 * Class MenuStorage
 * @package App\Module\Restaurant\Storage
 */
class MenuStorage extends Storage implements MenuStorageInterface {

    /**
     * @param string $slug
     * @return \App\Storage\Entity\EntityInterface|mixed|object|null
     */
    public function getMenuByRestaurantSlug(string $slug) {

        $menu = null;
        if ($this->storage instanceof MongoAdapter) {

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
                /*
                 // TODO REMOVE
                [
                    '$match' =>
                        ['menu.enable' => true],
                ],
                */
            ];

            /** @var Cursor $cursor */
            $cursor = $this->storage->getCollection('organization')->aggregate(
                $pipeline,
                ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
            );

            $arraySearch = $cursor->toArray();
            if (is_array($arraySearch) && count($arraySearch) > 0 && isset($arraySearch[0]['menu'])) {
                /** @var MenuEntity $menu */
                $menu =  clone $this->getEntityPrototype()->getPrototype($arraySearch[0]['menu']);
                $this->getHydrator()->hydrate($arraySearch[0]['menu'], $menu);
            }
        }

        return $menu;
    }
}