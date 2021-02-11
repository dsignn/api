<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Storage\Adapeter\Mongo;

use App\Storage\Adapter\Mongo\MongoAdapter;
use MongoDB\BSON\ObjectId;

/**
 * Class MenuMongoAdapter
 * @package App\Module\Restaurant\Storage\Adapeter\Mongo
 */
class MenuMongoAdapter extends MongoAdapter {

    /**
     * @param $search
     * @return array
     */
    protected function transformSearch(array $search){

        foreach ($search as $key => &$value) {

            switch ($key) {
                case 'organizations':
                    $ids = [];
                    foreach ($value as $id) {
                        array_push($ids, new ObjectId($id));
                    }
                    $search['organization._id'] = ['$in' => $ids];
                    unset($search[$key]);
                    break;
                case 'enable':
                    $search['enable'] =  $value === 'true' ? true : false;
                    break;
            }
        }

        return $search;
    }
}