<?php
declare(strict_types=1);

namespace App\Storage\ResultSet;

/**
 * Trait ResultSetPaginateAwareTrait
 * @package App\Storage\ResultSet
 */
trait ResultSetPaginateAwareTrait {

    /**
     * @var ResultSetPaginateInterface
     */
    protected $resultSetPaginate;

    /**
     * @return ResultSetPaginateInterface
     */
    public function getResultSet() {
        return $this->resultSetPaginate;
    }

    /**
     * @param ResultSetPaginateInterface $resultSetPaginate
     * @return self
     */
    public function setResultSet(ResultSetPaginateInterface $resultSetPaginate) {
        $this->resultSetPaginate = $resultSetPaginate;
        return $this;
    }
}