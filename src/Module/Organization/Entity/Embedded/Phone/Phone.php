<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity\Embedded\Phone;

/**
 * Class Phone
 * @package App\Module\Organization\Entity\Embedded\Phone
 */
class Phone {

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var int
     */
    protected $number;

    /**
     * @return string
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return Phone
     */
    public function setPrefix(string $prefix = null): Phone {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Phone
     */
    public function setNumber(int $number = null): Phone {
        $this->number = $number;
        return $this;
    }
}