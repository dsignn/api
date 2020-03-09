<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\ResultSet\ResultSetPaginateInterface;

/**
 * Class MongoResultSetPaginateInterface
 * @package App\Storage\Adapter\Mongo\ResultSet
 */
interface MongoResultSetPaginateInterface extends ResultSetPaginateInterface, MongoResultSetInterface { }