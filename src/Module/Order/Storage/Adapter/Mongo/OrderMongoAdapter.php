<?php
declare(strict_types=1);

namespace App\Module\Order\Storage\Adapter\Mongo;

use App\Module\Order\Entity\OrderEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Cursor;


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
                    
                    try {
                        $match = [
                            '$match' => [
                                '_id' => new ObjectId($value)
                            ]
                         ];

                        array_push($filter, $match);
                    } catch (Exception $exception) {
                        // TODO log???
                    }   
    
                    break;
                case 'organizations':
                    $ids = [];
                    foreach ($value as $id) {
                        array_push($ids, new ObjectId($id));
                    };

                    try {
                        $match = [
                            '$match' => [
                                'organization.id' => ['$in' => $ids]
                            ]
                        ];
                        array_push($filter, $match); 
                 
                    } catch (Exception $exception) {
                        // TODO log???
                    }   
                    break;
                case 'status':
                    
                    if ($value === 'for-kitchen') {
                        $match = [
                            '$match' => [
                                'status' => [
                                    '$in' =>  [
                                        OrderEntity::STATUS_PREPARATION,
                                        OrderEntity::STATUS_QUEUE,
                                    ]
                                ]

                            ]
                        ];
                    }
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
       
        $resultSet = clone $this->getResultSet();
        return $resultSet->setDataSource(
            $this->search($search)
        );
    }

        /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, array $search = [], array $order = []): ResultSetPaginateInterface {

        $resultSet = clone $this->getResultSetPaginate();
        return $resultSet->setPage($page)
            ->setItemPerPage($itemPerPage)
            ->setCount($this->getCount($search))
            ->setDataSource($this->search($search, $page, $itemPerPage));
    }

    /**
     * Template aggregation
     *
     * @param array $search
     * @return Cursor
     */
    protected function search(array $search, $page = null, $itemPerPage = null) {

        return  $this->getCollection()->aggregate(
            $this->getAggregationArraySearch($search, $page, $itemPerPage), 
            $this->arrayOptions);
    }

    /**
     * Undocumented function
     *
     * @param array $search
     * @return int
     */
    protected function getCount(array $search) {
        $aggreagate = $this->getAggregationArraySearch($search);
        array_push($aggreagate, ['$count' => 'total']);
        $cursor = $this->getCollection()->aggregate($aggreagate);
        $boison = $cursor->toArray();

        return count($boison) ? $boison[0]->total : 0;
    }

    /**
     * Undocumented function
     *
     * @param array $search
     * @return array
     */
    private function getAggregationArraySearch(array $search, $page = null, $itemPerPage = null) {
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
                                [ 'case' => [ '$eq' => [ OrderEntity::STATUS_VALIDATING,  '$status' ] ], 'then' => 0 ] ,
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
            // TODO ADD SORT AFTER FILTER
            [
                '$sort' => [
                    'computedStatus' => 1,
                    'created_at' => -1,
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
 
        // Pagination setting
        if ($page && $itemPerPage) {

            array_push($aggreagate, [ '$skip' => ($page-1) * $itemPerPage]);
            array_push($aggreagate, [ '$limit' => $itemPerPage]);
        }

        return $aggreagate;
    }
}




