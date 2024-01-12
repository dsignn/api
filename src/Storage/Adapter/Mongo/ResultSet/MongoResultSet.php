<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use MongoDB\Driver\Cursor;

/**
 * Class MongoResultSet
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
class MongoResultSet implements MongoResultSetInterface {

    /**
     * @var Cursor
     */
    protected $dataSource;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $index = 0;

    /**
     * @return Cursor
     */
    public function getDataSource(): Cursor {
        return $this->dataSource;
  
    }

    /**
     * @param Cursor $dataSource
     * @return MongoResultSetInterface
     * @throws \MongoConnectionException
     * @throws \MongoCursorTimeoutException
     */
    public function setDataSource(Cursor $dataSource): MongoResultSetInterface {

        $this->dataSource = $dataSource;
        $this->data = $dataSource->toArray();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function current() {
        return isset($this->data[$this->index]) ? $this->data[$this->index] : null;
    }

    /**
     * @inheritDoc
     */
    public function key() {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function next() : void {
        $this->index = $this->index + 1;
        $this->data[$this->index];
    }

    /**
     * @inheritDoc
     */
    public function rewind() : void {
        $this->index = 0;
    }

    /**
     * @inheritDoc
     */
    public function valid() : bool {
        return isset($this->data[$this->index]);
    }

    /**
     * @inheritDoc
     */
    public function count() : int {
        return count($this->data);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array {
        return $this->data;
    }
}