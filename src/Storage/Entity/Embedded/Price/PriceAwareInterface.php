<?php
declare(strict_types=1);

namespace App\Storage\Entity\Embedded\Price;

use App\Storage\Entity\Embedded\Price\PriceInterface;

/**
 * interface PriceAwareInterface
 */
interface PriceAwareInterface {

     /**
     * @return float
     */
    public function getPrice(): PriceInterface;

    /**
     * @param float $value
     * @return self
     */
    public function setPrice(PriceInterface $price);

}