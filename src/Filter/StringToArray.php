<?php
declare(strict_types=1);

namespace App\Filter;

use Laminas\Filter\FilterInterface;

/**
 * Class StringToArray
 * @package App\Module\Resource\Filter
 */
class StringToArray implements FilterInterface {

    /**
     * @var string
     */
    protected $delimiter = ',';

    /**
     * @param mixed $value
     * @return mixed|void
     */
    public function filter($value) {

        switch (true) {
            case (gettype($value) === 'string' ):
                $value =  explode($this->delimiter, $value);
                break;
            case $value === null:
                $value = [];
                break;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return StringToArray
     */
    public function setDelimiter(string $delimiter): StringToArray {
        $this->delimiter = $delimiter;
        return $this;
    }


}