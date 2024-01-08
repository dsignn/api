<?php
declare(strict_types=1);

namespace App\Storage\ResultSet;

/**
 * interface ResultSetPaginateAwareTrait
 * @package App\Storage\ResultSet
 */
interface ResultSetPaginateAwareInterface {

    /**
     * @return ResultSetPaginateInterface
     */
    public function getResultSetPaginate() : ResultSetPaginateInterface;

    /**
     * @param ResultSetPaginateInterface $resultSetPaginate
     * @return self
     */
    public function setResultSetPaginate(ResultSetPaginateInterface $resultSetPaginate) : self;
}