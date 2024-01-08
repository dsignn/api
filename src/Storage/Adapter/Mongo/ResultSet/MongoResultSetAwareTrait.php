<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

/**
 * Trait MongoResultSetAwareTrait
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
trait MongoResultSetAwareTrait {

    /**
     * @var MongoResultSetInterface
     */
    protected $resultSet;

    /**
     * @return MongoResultSetInterface
     */
    public function getResultSet() {
        return $this->resultSet;
    }

    /**
     * @param MongoResultSetInterface $resultSet
     * @return self
     */
    public function setResultSet(MongoResultSetInterface $resultSet) {
        $this->resultSet = $resultSet;
        return $this;
    }
}