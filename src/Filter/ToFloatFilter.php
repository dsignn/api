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
                $value = round(floatval($value), 2);
                break;
            case is_string($value) === true:
                $value = str_replace(",", ".", $value);
                $value = round(floatval($value), 2);
                break;
            default:
                // TODO DA CAPIRE SE E' IL CASO
                $value = round(floatval($value), 2);
        }

        return $value;
    }
}