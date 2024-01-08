<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

/**
 * Trait MongoResultSetPaginateAwareTrait
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
trait MongoResultSetPaginateAwareTrait {

    /**
     * @var MongoResultSetPaginateInterface
     */
    protected $resultSetPaginate;

    /**
     * @return MongoResultSetPaginateInterface
     */
    public function getResultSetPaginate() {
        return $this->resultSetPaginate;
    }

    /**
     * @param MongoResultSetPaginateInterface $resultSetPaginate
     * @return self
     */
    public function setResultSetPaginate(MongoResultSetPaginateInterface $resultSetPaginate) {
        $this->resultSetPaginate = $resultSetPaginate;
        return $this;
    }
}