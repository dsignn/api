<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

/**
 * Interface MongoResultSetAwareInterface
 * @package App\Storage\ResultSet
 */
interface MongoResultSetAwareInterface {

    /**
     * @return MongoResultSetInterface
     */
    public function getResultSet();

    /**
     * @param MongoResultSetInterface $resultSet
     * @return self
     */
    public function setResultSet(MongoResultSetInterface $resultSet);
}