<?php
declare(strict_types=1);

namespace App\Storage\ResultSet;

use Countable;
use Iterator;

/**
 * Interface ResultSetPaginateInterface
 * @package App\Storage\ResultSet
 */
interface ResultSetPaginateInterface extends ResultSetInterface {
    /**
     * @return mixed
     */
    public function getPage();

    /**
     * @param $page
     * @return ResultSetPaginateInterface
     */
    public function setPage($page): self;

    /**
     * @return mixed
     */
    public function getItemPerPage();

    /**
     * @param $itemPerPage
     * @return ResultSetPaginateInterface
     */
    public function setItemPerPage($itemPerPage): self;
}