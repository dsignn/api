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
     * @return int
     */
    public function getPage();

    /**
     * @param $page
     * @return self
     */
    public function setPage(int $page): self;

    /**
     * @return int
     */
    public function getItemPerPage();

    /**
     * @param $itemPerPage
     * @return self
     */
    public function setItemPerPage(int $itemPerPage): self;
}