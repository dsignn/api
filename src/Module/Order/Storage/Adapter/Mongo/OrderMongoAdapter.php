<?php
declare(strict_types=1);

namespace App\Module\Order\Storage\Adapter\Mongo;

use App\Module\Order\Entity\OrderEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\ResultSet\ResultSetInterface;
use MongoDB\BSON\ObjectId;

use function Aws\filter;

/**
 * Class OrderMongoAdapter
 * @package App\Module\Order\Storage\Adapeter\Mongo
 */
class OrderMongoAdapter extends MongoAdapter {

    /**
     * @param $search
     * @return array
     */
    protected function transformSearch(array $search){

        $filter =  [];
        foreach ($search as $key => &$value) {

            switch ($key) {
                case 'id':
                                    // TODO add try
                    $match = [
                        '$match' => [
                            '_id' => new ObjectId($value)
                        ]
                    ];

                    array_push($filter, $match);
                    break;
                case 'organizations':
                    $ids = [];
                    foreach ($value as $id) {
                        array_push($ids, new ObjectId('6076e79969205a3f58735397'));
                    };

                    $match = [
                        '$match' => [
                            'organization.id' => ['$in' => $ids]
                        ]
                    ];
                    array_push($filter, $match); 
                    break;
            }
        }
        return $filter;
    }
        /**
     * @inheritDoc
     */
    public function getAll(array $search = [], array $order = []): ResultSetInterface {

        $aggreagate = [
            [
                '$project' => [
                    "_id"  =>1,
                    "organization"  => 1,
                    "status"  => 1,
                    "last_update_at"  => 1,
                    "created_at"  => 1,
                    "items"  => 1,
                    "additional_info"  => 1,
                    "name"  => 1,
                    "computedStatus" => [
                        '$switch' => [
                            'branches' => [
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_CAN_ORDER,  '$status' ] ], 'then' => 1 ] ,
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_QUEUE,  '$status' ] ], 'then' => 2 ],
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_PREPARATION,  '$status' ] ], 'then' => 3 ],
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_DELIVERING,  '$status' ] ], 'then' => 4 ],
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_CLOSE,  '$status' ] ], 'then' => 5 ],
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_INVALID,  '$status' ] ], 'then' => 6 ]          
                            ],
                            'default' => 1000000
                        ]
                    ]
                ]
            ],
            [
                '$sort' => [
                    'computedStatus' => 1,
                    'last_update_at' => -1
                ]
            ]

        ];
            

        $filter = $this->transformSearch($search);
        if (count($filter) > 0) {
            for ($cont = 0;  count($filter) > $cont; $cont++) {
                array_unshift($aggreagate, $filter[$cont]);
            }
        }
       
        $resultSet = clone $this->getResultSet();
        return $resultSet->setDataSource(
            $this->getCollection()->aggregate($aggreagate, $this->arrayOptions)
        );
    }
}




