<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use MongoCursor;

/**
 * Class MongoResultSet
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
class MongoResultSet implements MongoResultSetInterface {

    /**
     * @var MongoCursor
     */
    protected $dataSource;

    /**
     * @return MongoCursor
     */
    public function getDataSource(): MongoCursor
    {
        return $this->dataSource;
    }

    /**
     * @param MongoCursor $dataSource
     * @return MongoResultSet
     */
    public function setDataSource(MongoCursor $dataSource): MongoResultSetInterface {
        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function current() {
        return $this->dataSource->current();
    }

    /**
     * @inheritDoc
     */
    public function key() {
        return $this->dataSource->key();
    }

    /**
     * @inheritDoc
     */
    public function next() {
        return $this->dataSource->next();
    }

    /**
     * @inheritDoc
     */
    public function rewind() {
        return $this->dataSource->reset();
    }

    /**
     * @inheritDoc
     */
    public function valid() {
        return $this->dataSource->valid();
    }

    /**
     * @inheritDoc
     */
    public function count() {
        return $this->dataSource->count();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array {
        return iterator_to_array($this->dataSource);
    }
}