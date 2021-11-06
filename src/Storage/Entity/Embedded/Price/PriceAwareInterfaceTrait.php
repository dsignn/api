<?php
declare(strict_types=1);

namespace App\Storage\Entity\Embedded\Price;

use App\Storage\Entity\Embedded\Price\PriceInterface;

/**
 * trait PriceAwareInterfaceTrait
 */
trait PriceAwareInterfaceTrait {

    protected $price;

     /**
     * @return float
     */
    public function getPrice(): PriceInterface {
        return $this->price;
    }

    /**
     * @param float $value
     * @return self
     */
    public function setPrice(PriceInterface $price): self {
        $this->price = $price;
        return $this;
    }
}