<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

/**
 * Interface MongoResultSetPaginateAwareInterface
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
interface MongoResultSetPaginateAwareInterface {

    /**$resultSetPaginate
     * @return MongoResultSetPaginateInterface
     */
    public function getResultSetPaginate();

    /**
     * @param MongoResultSetPaginateInterface $resultSetPaginate
     * @return self
     */
    public function setResultSetPaginate(MongoResultSetPaginateInterface $resultSetPaginate);
}