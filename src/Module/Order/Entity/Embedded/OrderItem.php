<?php
declare(strict_types=1);

namespace App\Module\Order\Entity\Embedded;

use App\Storage\Entity\Embedded\PriceInterface;
use App\Storage\Entity\Embedded\Price;

/**
 * Class OrderItem
 * @package App\Module\Order\Embedded
 */
class OrderItem {

    /**
     * @var integer
     */
    protected $quantity = 0;
    
    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * Get the value of quantity
     *
     * @return  integer
     */ 
    public function getQuantity(): int {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @param  integer  $quantity
     * @return  self
     */ 
    public function setQuantity($quantity): OrderItem {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get the value of price
     *
     * @return  string
     */ 
    public function getPrice(): PriceInterface {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @param  string  $price
     * @return  self
     */ 
    public function setPrice(PriceInterface $price) {
        $this->price = $price;
        return $this;
    }
}