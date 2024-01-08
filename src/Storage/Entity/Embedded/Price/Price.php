<?php
declare(strict_types=1);

namespace App\Storage\Entity\Embedded\Price;

/**
 * class Price
 * @package namespace App\Storage\Entity\Embedded
 */
class Price implements PriceInterface {

    /**
     * @var float
     */
    protected $value = 0;

    /**
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @return int
     */
    public function getValue(): float {
        return $this->value;
    }

    /**
     * @param float $value
     * @return Price
     */
    public function setValue(float $value): PriceInterface {
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
    public function setCurrency(string $currency): PriceInterface {
        $this->currency = $currency;
        return $this;
    }
}