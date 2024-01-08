<?php
declare(strict_types=1);

namespace App\Filter;

use Laminas\Filter\FilterInterface;

/**
 * Class ToStringFilter
 * @package App\Filter
 */
class ToStringFilter implements FilterInterface {

    /**
     * @param mixed $value
     * @return mixed|void
     */
    public function filter($value) {
        if ($value === null) {
            $value = '';
        }

        return $value;
    }
}