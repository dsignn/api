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
                case 'name':
                    $search[$key] = new \MongoDB\BSON\Regex($search[$key], 'i');
                    break;
            }
        }

        return $search;
    }
}