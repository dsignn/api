<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo;

use App\Storage\Adapter\Mongo\ResultSet\MongoResultSet;
use App\Storage\Adapter\Mongo\ResultSet\MongoResultSetAwareInterface;
use App\Storage\Adapter\Mongo\ResultSet\MongoResultSetAwareTrait;
use App\Storage\Adapter\Mongo\ResultSet\MongoResultSetPaginateAwareInterface;
use App\Storage\Adapter\Mongo\ResultSet\MongoResultSetPaginateAwareTrait;
use App\Storage\Adapter\StorageAdapterInterface;
use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use MongoDB\BSON\ObjectId;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\DeleteResult;
use MongoDB\Driver\Cursor;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;

/**
 * Class MongoAdapter
 */
class MongoAdapter implements StorageAdapterInterface, MongoResultSetAwareInterface, MongoResultSetPaginateAwareInterface  {

    use MongoResultSetAwareTrait, MongoResultSetPaginateAwareTrait;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var string
     */
    protected $collectionName;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $arrayOptions = ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']];

    /**
     * MongoCollection constructor.
     * @param Client $client
     * @param string $dbName
     * @param string $collectionName
     */
    public function __construct(Client $client, string $dbName, string $collectionName) {

        $this->client = $client;

        $this->dbName = $dbName;

        $this->collectionName = $collectionName;

        $this->setResultSet(new MongoResultSet());
    }

    /**
     * @param string $name
     * @return Collection
     */
    public function getCollection(string $name = null) {
        $name = $name ? $name : $this->collectionName;
        return $this->client->{$this->dbName}->{$name};
    }

    /**
     * @inheritDoc
     */
    public function get($id) {

        return $this->getCollection()->findOne(
            ['_id' => $this->generateId($id)],
            $this->arrayOptions
        );
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): array {

        unset($data['_id']);
        /** @var InsertOneResult $result */
        $result = $this->getCollection()->insertOne($data);
        if (!$result->isAcknowledged()) {
            throw new \Exception('TODOOOOOOOOOOOOOOOOOOOO SAVE');
        }
        $data['_id'] = $result->getInsertedId();
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function update(array $data): array {

        if (!isset($data['_id'])) {
            // TODO
            throw new \Exception('Id not set', 500);
        }

        /** @var UpdateResult $result */
        $result = $this->getCollection()->updateOne(
            ["_id" =>   $data['_id']],
            ['$set' => $data],
            ['upsert' => true]
        );

        if (!$result->isAcknowledged()) {
            throw new \Exception('TODO  LOG');
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool {
        /** @var DeleteResult $result */
        $result = $this->getCollection()->deleteOne(['_id' => $this->generateId($id)]);

        if (!$result->isAcknowledged()) {
            throw new \Exception('TODO ADD LOG');
        }

        return !!$result->getDeletedCount();
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = [], array $order = []): ResultSetInterface {
        $resultSet = clone $this->getResultSet();
        return $resultSet->setDataSource(
            $this->searchDataSource(
                $search,
                $order
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, array $search = [], array $order = []): ResultSetPaginateInterface {

        $resultSet = clone $this->getResultSetPaginate();
        return $resultSet->setPage($page)
            ->setItemPerPage($itemPerPage)
            ->setCount($this->getCount($this->transformSearch($search)))
            ->setDataSource(
                $this->searchDataSource(
                    $search,
                    $order,
                    $itemPerPage,
                    ($page-1)*$itemPerPage
                )
            );
    }

    /**
     * @param array $search
     * @param array $sort
     * @param null $limit
     * @param null $skip
     * @return Cursor
     */
    protected function searchDataSource(array $search, array $order = [], $limit = null, $skip = null) {

        $options = array_merge($this->arrayOptions, []);

        if ($limit !== null) {
            $options['limit'] = $limit;
        }

        if ($skip !== null) {
            $options['skip'] = $skip;
        }

        if (count($order)) {
            $options['sort'] = $order;
        }

        return $this->getCollection()->find(
            $this->transformSearch($search),
            $options
        );
    }

    /**
     * @param $search
     * @return mixed
     */
    protected function transformSearch(array $search) {

        return $search;
    }

    /**
     * @param $search
     * @return mixed
     */
    protected function getCount(array  $search) {
        return $this->getCollection()->count($search);
    }

    /**
     * @param $id
     * @return ObjectId
     */
    private function generateId($id) {
        try {
            $id = new ObjectId($id);
        } catch(\Exception $e) {
            // log
        }

        return $id;
    }
}