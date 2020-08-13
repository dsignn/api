<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Entity\Embedded\Price;


class Price {

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }

    /**
     * @param int $value
     * @return Price
     */
    public function setValue(int $value): Price {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Price
     */
    public function setCurrency(string $currency): Price {
        $this->currency = $currency;
        return $this;
    }
}