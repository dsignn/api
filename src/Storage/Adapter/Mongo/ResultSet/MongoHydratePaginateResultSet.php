<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\ObjectPrototypeInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Laminas\Hydrator\HydratorAwareInterface;

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
     * @var int
     */
    protected $count = 0;

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

    /**
     * @inheritDoc
     */
    public function setCount(int $count): ResultSetPaginateInterface {
        $this->count = $count;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count() {
        return $this->count;
    }
}