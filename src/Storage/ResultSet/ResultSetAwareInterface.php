<?php
declare(strict_types=1);

namespace App\Storage\ResultSet;

/**
 * Interface ResultSetAwareInterface
 * @package App\Storage\ResultSet
 */
interface ResultSetAwareInterface {

    /**
     * @return ResultSetInterface
     */
    public function getResultSet();

    /**
     * @param ResultSetInterface $resultSet
     * @return self
     */
    public function setResultSet(ResultSetInterface $resultSet);
}