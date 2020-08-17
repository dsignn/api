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
use MongoDB\Driver\Query;
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
     * @return Collection
     */
    public function getCollection() {
        return $this->client->{$this->dbName}->{$this->collectionName};
    }

    /**
     * @inheritDoc
     */
    public function get($id) {
        return $this->getCollection()->findOne(
            ['_id' => new ObjectId($id)],
            ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
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
            throw new \Exception('TODOOOOOOOOOOOOOOOOOOOO UPDATE');
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool {
        /** @var DeleteResult $result */
        $result = $this->getCollection()->deleteOne(['_id' => new ObjectId($id)]);

        if (!$result->isAcknowledged()) {
            throw new \Exception('TODOOOOOOOOOOOOOOOOOOOO DELETE');
        }

        return !!$result->getDeletedCount();
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = []): ResultSetInterface {
        $resultSet = clone $this->getResultSet();
        return $resultSet->setDataSource(
            $this->searchDataSource(
                $search
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, array $search = []): ResultSetPaginateInterface {

        $resultSet = clone $this->getResultSetPaginate();

        return $resultSet->setPage($page)
            ->setItemPerPage($itemPerPage)
            ->setCount($this->getCount($search))
            ->setDataSource(
                $this->searchDataSource(
                    $search,
                    $itemPerPage,
                    ($page-1)*$itemPerPage
                )
            );
    }

    /**
     * @param array $search
     * @param null $limit
     * @param null $skip
     * @return Cursor
     */
    protected function searchDataSource(array $search, $limit = null, $skip = null) {

        $options = [
            'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
        ];

        if ($limit !== null) {
            $options['limit'] = $limit;
        }

        if ($skip !== null) {
            $options['skip'] = $skip;
        }

        return $this->getCollection()->find(
            $search,
            $options
        );
    }

    /**
     * @param $search
     * @return mixed
     */
    protected function getCount(array  $search) {
        return $this->getCollection()->count($search);
    }
}