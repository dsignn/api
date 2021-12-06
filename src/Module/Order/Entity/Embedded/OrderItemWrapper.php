<?php
declare(strict_types=1);

namespace App\Module\Order\Entity\Embedded;

use App\Storage\Entity\Embedded\Price\PriceInterface;
use App\Storage\Entity\Embedded\Price\Price;

/**
 * Class OrderItemWrapper
 * @package App\Module\Order\Entity\Embedded
 */
class OrderItemWrapper {

    /**
     * @var integer
     */
    protected $quantity = 0;
    
    /**
     *
     * @var object
     */
    protected $orderedItem;

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
    public function setQuantity($quantity): OrderItemWrapper {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get the value of item
     *
     * @return  object
     */ 
    public function getOrderedItem() {
        return $this->orderedItem;
    }

    /**
     * Set the value of item
     *
     * @param  object  $orderedItem
     * @return  self
     */ 
    public function setOrderedItem($orderedItem): OrderItemWrapper {
        $this->orderedItem = $orderedItem;
        return $this;
    }
}