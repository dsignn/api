<?php
declare(strict_types=1);

namespace App\Storage\ResultSet;

/**
 * Interface ResultSetAwareInterface
 * @package App\Storage\ResultSet
 */
trait ResultSetAwareTrait {

    /**
     * @var ResultSetInterface
     */
    protected $resultSet;

    /**
     * @return ResultSetInterface
     */
    public function getResultSet() {
        return $this->resultSet;
    }

    /**
     * @param ResultSetInterface $resultSet
     * @return self
     */
    public function setResultSet(ResultSetInterface $resultSet) {
        $this->resultSet = $resultSet;
        return $this;
    }
}