<?php
declare(strict_types=1);

namespace App\Filter;


use Laminas\Filter\FilterInterface;

/**
 * Class ToFloatFilter
 * @package App\Filter
 */
class ToFloatFilter implements FilterInterface {

    /**
     * @inheritDoc
     */
    public function filter($value) {

        switch (true) {
            case $value == "":
            case $value  === null:
                $value = 0;
                break;
            case is_numeric($value) === true:
                $value = floatval($value);
                break;
        }

        return $value;
    }
}