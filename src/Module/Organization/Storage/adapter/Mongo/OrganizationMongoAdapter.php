<?php
declare(strict_types=1);

namespace App\Module\Organization\Storage\adapter\Mongo;

use App\Storage\Adapter\Mongo\MongoAdapter;
use MongoDB\BSON\ObjectId;

/**
 * Class OrganizationMongoAdapter
 * @package App\Module\Organization\Storage\adapter\Mongo
 */
class OrganizationMongoAdapter extends MongoAdapter {

    /**
     * @param array $search
     * @return array|mixed
     */
    protected function transformSearch(array $search) {

        foreach ($search as $key => &$value) {

            switch ($key) {
                case 'organizations':
                    $ids = [];
                    foreach ($value as $id) {
                        array_push($ids, new ObjectId($id));
                    }
                    $search['_id'] = ['$in' => $ids];
                    unset($search[$key]);
                    break;
            }
        }

        return $search;
    }
}