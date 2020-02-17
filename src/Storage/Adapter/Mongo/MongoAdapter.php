<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo;

use App\Storage\Adapter\Mongo\ResultSet\MongoResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoResultSetAwareInterface;
use App\Storage\Adapter\Mongo\ResultSet\MongoResultSetAwareTrait;
use App\Storage\Entity\EntityInterface;
use App\Storage\StorageInterface;
use MongoClient;
use MongoId;

/**
 * Class MongoAdapter
 */
class MongoAdapter implements StorageInterface, MongoResultSetAwareInterface  {

    use MongoResultSetAwareTrait;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var string
     */
    protected $collectionName;

    /**
     * @var MongoClient
     */
    protected $client;

    /**
     * MongoCollection constructor.
     * @param MongoClient $client
     * @param string $dbName
     * @param string $collectionName
     */
    public function __construct(MongoClient $client, string $dbName, string $collectionName) {

        $this->client = $client;

        $this->dbName = $dbName;

        $this->collectionName = $collectionName;

        $this->setResultSet(new MongoResultSet());
    }

    /**
     * @return \MongoCollection
     */
    protected function getCollection() {
        return $this->client->{$this->dbName}->{$this->collectionName};
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->getCollection()->findOne(
            ['_id' => new MongoId($id)]
        );
    }

    /**
     * @inheritDoc
     */
    public function save(&$data) {
        $dbInfo = $this->getCollection()->insert($data);
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function update(&$data) {
        throw new \Exception('Implements');
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $obj) {
        $dbInfo = $this->getCollection()->remove(['_id' => new \MongoId($obj->getId())]);
        if ($dbInfo['errmsg'] !== null) {
            throw new \MongoException($dbInfo['errmsg']);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function gelAll(array $search = null) {
        $resultSet = clone $this->getResultSet();
        return $resultSet->setDataSource($this->getCollection()->find($this->search($search ?  $search : [])));
    }

    /**
     * @param $page
     * @param $itemPerPage
     * @param $search
     */
    public function paginate($page, $itemPerPage, $search) {

    }

    /**
     * @param array $search
     * @return array
     */
    public function search($search) {
        return $search;
    }

}