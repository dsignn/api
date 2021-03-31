<?php
declare(strict_types=1);

namespace App\Filter;

use Laminas\Filter\Exception;
use Laminas\Filter\FilterInterface;

/**
 * Class DefaultFilter
 * @package App\Filter
 */
class DefaultFilter implements FilterInterface {

    /**
     * @var
     */
    protected $default;

    /**
     * DefaultFilter constructor.
     * @param $default
     */
    public function __construct($default) {

        $this->default = $default;
    }

    /**
     * @param mixed $value
     * @return mixed|void
     */
    public function filter($value) {
        // TODO: Implement filter() method.
        if (!$value) {
            $value = $this->default;
        }

        return $value;
    }
}