<?php
declare(strict_types=1);

namespace App\Module\Order\Entity\Embedded;

use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Storage\Entity\Embedded\Price\PriceAwareInterface;
use App\Storage\Entity\Embedded\Price\PriceAwareInterfaceTrait;

/**
 * Class MenuOrder
 * @package App\Module\Order\Entity\Embedded
 */
class CarOrder implements PriceAwareInterface {

    use PriceAwareInterfaceTrait;

    const TYPE_MENU = 'car-order';

    /**
     * @var [string]
     */
    protected $type= self::TYPE_MENU;

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }
}