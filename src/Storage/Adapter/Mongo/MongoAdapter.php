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
use MongoClient;
use MongoId;

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
    public function get($id) {
        return $this->getCollection()->findOne(
            ['_id' => new MongoId($id)]
        );
    }

    /**
     * @inheritDoc
     */
    public function save(array $data): array {
        $dbInfo = $this->getCollection()->insert($data);
        if ($dbInfo['errmsg'] !== null) {
            throw new \MongoException($dbInfo['errmsg']);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function update(array $data): array {
        $dbInfo = $this->getCollection()->update(
            ["_id" => $data["_id"] ? $data['_id'] :  ''],
            $data
        );
        if ($dbInfo['errmsg'] !== null) {
            throw new \MongoException($dbInfo['errmsg']);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool {
        $dbInfo = $this->getCollection()->remove(['_id' => new \MongoId($id)]);
        if ($dbInfo['errmsg'] !== null) {
            throw new \MongoException($dbInfo['errmsg']);
        }
        return !!$dbInfo['ok'];
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = null): ResultSetInterface {
        $resultSet = clone $this->getResultSet();
        return $resultSet->setDataSource(
            $this->searchDataSource(
                $search ?  $search : []
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = null): ResultSetPaginateInterface {

        $resultSet = clone $this->getResultSetPaginate();


        return $resultSet->setPage($page)
            ->setItemPerPage($itemPerPage)
            ->setDataSource(
                $this->searchDataSource(
                    $search ?  $search : [],
                    $itemPerPage,
                    ($page-1)*$itemPerPage
                )
            );
    }

    /**
     * @param $search
     * @param null $limit
     * @param null $skip
     * @return \MongoCursor
     * @throws \MongoCursorException
     */
    protected function searchDataSource(array $search, $limit = null, $skip = null) {

        foreach ($search as $key => $value) {
            switch ($key) {
                case 'page':
                case  'item-per-page':
                    unset($search[$key]);
                    break;
            }
        }

        $dataSource = $this->getCollection()->find(
            $search
        );

        if ($limit !== null) {
            $dataSource->limit($limit);
        }

        if ($skip !== null) {
            $dataSource->skip($skip);
        }

        return $dataSource;
    }
}