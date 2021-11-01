<?php
declare(strict_types=1);

namespace App\Storage\Entity\Embedded;

/**
 * interface PriceInterface
 */
interface PriceInterface {

     /**
     * @return float
     */
    public function getValue(): float;

    /**
     * @param float $value
     * @return Price
     */
    public function setValue(float $value): PriceInterface;

    /**
     * @return string
     */
    public function getCurrency(): string;

    /**
     * @param string $currency
     * @return PriceInterface
     */
    public function setCurrency(string $currency): PriceInterface;
}