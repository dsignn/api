<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Storage;

use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Storage;
use MongoDB\BSON\ObjectId;
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
                        'localField' => '_id',
                        'foreignField' => 'organization._id',
                        'as' => 'menu'
                    ]
                ],
                [
                    '$unwind' => [
                        "path" => '$menu',
                        "preserveNullAndEmptyArrays"=> true
                    ]
                ],
                [
                    '$match' =>
                        ['menu.enable' => true],
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
                        "path" => '$logo',
                        "preserveNullAndEmptyArrays"=> true
                    ]
                ]
            ];

            /** @var Cursor $cursor */
            $cursor = $this->storage->getCollection('organization')->aggregate(
                $pipeline,
                ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
            );

            $resourceIds = [];

            $arraySearch = $cursor->toArray();
            var_dump($arraySearch);
            die();

            if (is_array($arraySearch) && count($arraySearch) > 0 && isset($arraySearch[0]['menu'])) {

                $menu =  $this->extractMenuArray($arraySearch);
                $ids = $this->extractResourceId($menu);
                $resources = $this->getResourceByIds($ids);
                $this->injectResourceInMenu($menu, $resources);
            }
        }

        return $this->extractMenu($menu);
    }

    /**
     * @param array $menu
     * @return array
     */
    protected function extractMenu(array $menu) {

        if (isset($menu['_id']) && $menu['_id'] instanceof ObjectId) {
            $menu['_id'] = $menu['_id']->__toString();
        }

        if (isset($menu['organization']['_id']) && $menu['organization']['_id'] instanceof ObjectId) {
            $menu['organization']['_id'] = $menu['organization']['_id']->__toString();
        }

        if (isset($menu['organization']['logo']['_id']) && $menu['organization']['logo']['_id'] instanceof ObjectId) {
            $menu['organization']['logo']['_id'] = $menu['organization']['logo']['_id']->__toString();
        }

        if (isset($menu['organization']['qr_code']['id']) && $menu['organization']['qr_code']['id'] instanceof ObjectId) {
            $menu['organization']['qr_code']['id'] = $menu['organization']['qr_code']['id']->__toString();
        }

        for ($index = 0; $index < count($menu['items']); $index++) {
            //var_dump($menu['items'][$index]);
            if (isset($menu['items'][$index]['_id']) && $menu['items'][$index]['_id'] instanceof ObjectId) {
                $menu['items'][$index]['_id'] = $menu['items'][$index]['_id']->__toString();
            }

            if(isset($menu['items'][$index]['photos']) && is_array($menu['items'][$index]['photos'])) {
                for ($index2 = 0; $index2 < count($menu['items'][$index]['photos']); $index2++) {
                    if (isset($menu['items'][$index]['photos'][$index2]['_id']) && $menu['items'][$index]['photos'][$index2]['_id'] instanceof ObjectId) {
                        $menu['items'][$index]['photos'][$index2]['_id'] = $menu['items'][$index]['photos'][$index2]['_id']->__toString();
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * @param array $menu
     * @param array $resources
     */
    protected function injectResourceInMenu(array &$menu, array $resources) {

        for ($index = 0; $index < count($menu['items']); $index++) {
            if (isset($menu['items'][$index]['photos']) && is_array($menu['items'][$index]['photos'])) {
                for ($index2 = 0; $index2 < count($menu['items'][$index]['photos']); $index2++) {
                    if ($menu['items'][$index]['photos'][$index2]['id']) {
                        foreach ($resources as $resource) {
                            if ($menu['items'][$index]['photos'][$index2]['id'] == $resource['_id']) {
                                $menu['items'][$index]['photos'][$index2] = $resource;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function getResourceByIds(array $ids) {
        /** @var Cursor $cursor */
        $cursor = $this->storage->getCollection('resource')->find([
                '_id' => [
                    '$in' => $ids
                ]
            ],
            ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
        );

        return $cursor->toArray();
    }

    /**
     * @param array $arrayAggregation
     * @return array
     */
    protected function extractMenuArray(array $arrayAggregation) {
        $menu =  $arrayAggregation[0]['menu']; // clone $this->getEntityPrototype()->getPrototype($arraySearch[0]['menu']);
        unset($arrayAggregation[0]['menu']);
        //$this->getHydrator()->hydrate($arraySearch[0]['menu'], $menu);
        $menu['organization'] = $arrayAggregation[0];
        return $menu;
    }

    /**
     * @param array $menu
     * @return array
     */
    protected function extractResourceId(array $menu) {

        $ids = [];
        for ($index = 0; $index < count($menu['items']); $index++) {

            if (isset($menu['items'][$index]['photos']) && is_array($menu['items'][$index]['photos'])) {
                foreach ($menu['items'][$index]['photos'] as $photo) {
                    if ($photo['id']) {
                        array_push($ids, $photo['id']);
                    }
                }
            }
        }
        return $ids;
    }
}