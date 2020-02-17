<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\ResultSet\ResultSetInterface;
use MongoCursor;

/**
 * Class MongoResultSet
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
interface MongoResultSetInterface extends ResultSetInterface {

    /**
     * @param MongoCursor $dataSource
     * @return mixed
     */
    public function setDataSource(MongoCursor $dataSource): MongoResultSetInterface;

    /**
     * @return MongoCursor
     */
    public function getDataSource(): MongoCursor;
}