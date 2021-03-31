<?php
declare(strict_types=1);

namespace App\Filter;

use DateTime;
use Laminas\Filter\Exception;
use Laminas\Filter\FilterInterface;

class ToDateFilter implements FilterInterface {

    /**
     * @var string
     */
    protected $format = 'd-m-Y';

    public function __construct(array $options = null) {

        if ($options && isset($options['format'])) {
            $this->format = $options['format'];
        }
    }

    public function filter($value) {
        // TODO: Implement filter() method.
        if ($value === '' || $value === null) {
            return $value;
        }

        if (!is_string($value) && !is_int($value)) {
            return $value;
        }

        try {
            return $this->filterDate($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * @param $value
     * @return \DateTime
     * @throws \Exception
     */
    protected function filterDate($value) {

        switch (true) {
            case is_int($value) === true:
                $value = new \DateTime('@' . $value);
                break;
            case is_string($value) === true:
                $tmpDate = DateTime::createFromFormat($this->format, $value);
                if ($tmpDate) {
                    $value = $tmpDate;
                }
                break;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getFormat(): string {
        return $this->format;
    }

    /**
     * @param string $format
     * @return ToDateFilter
     */
    public function setFormat(string $format): ToDateFilter {
        $this->format = $format;
        return $this;
    }
}