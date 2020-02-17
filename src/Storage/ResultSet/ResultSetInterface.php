<?php
declare(strict_types=1);

namespace App\Storage\ResultSet;

use Countable;
use Iterator;

/**
 * Interface ResultSetInterface
 * @package App\Storage\ResultSet
 */
interface ResultSetInterface extends Countable, Iterator {

    /**
     * @return array
     */
    public function toArray(): array ;
}