<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\ResultSet\ResultSetInterface;
use MongoCursor;
use MongoDB\Driver\Cursor;

/**
 * Class MongoResultSet
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
interface MongoResultSetInterface extends ResultSetInterface {

    /**
     * @param Cursor $dataSource
     * @return MongoResultSetInterface
     */
    public function setDataSource(Cursor $dataSource): MongoResultSetInterface;

    /**
     * @return Cursor
     */
    public function getDataSource(): Cursor;
}