<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\ObjectPrototypeInterface;
use App\Storage\ObjectPrototypeTrait;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Zend\Hydrator\HydratorAwareInterface;
use Zend\Hydrator\HydratorAwareTrait;

/**
 * Class MongoHydrateResultSet
 * @package App\Storage\Mongo
 */
class MongoHydratePaginateResultSet extends MongoHydrateResultSet implements MongoResultSetPaginateInterface, HydratorAwareInterface, ObjectPrototypeInterface {

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $itemPerPage;

    /**
     * @return int
     */
    public function getPage(): int {
        return $this->page;
    }

    /**
     * @param int $page
     * @return ResultSetPaginateInterface
     */
    public function setPage(int $page): ResultSetPaginateInterface {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemPerPage(): int {
        return $this->itemPerPage;
    }

    /**
     * @param int $itemPerPage
     * @return ResultSetPaginateInterface
     */
    public function setItemPerPage(int $itemPerPage): ResultSetPaginateInterface {
        $this->itemPerPage = $itemPerPage;
        return $this;
    }
}