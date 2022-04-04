<?php

declare(strict_types=1);

namespace App\Module\Restaurant\Entity\Embedded;

use App\Module\Restaurant\Entity\MenuEntity;
use App\Storage\Entity\Embedded\Price\Price;
use App\Storage\Entity\Embedded\Price\PriceAwareInterface;
use App\Storage\Entity\Embedded\Price\PriceAwareInterfaceTrait;
use App\Storage\Entity\Embedded\Price\PriceInterface;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;
use App\Storage\Entity\ReferenceInterface;

/**
 * Class FixedMenu
 * @package App\Module\Restaurant\Entity\Embedded
 */
class FixedMenu implements PriceAwareInterface {

    use PriceAwareInterfaceTrait;

    /**
     * @var boolean
     */
    protected $enable = false;

    /**
     * Get the value of enable
     *
     * @return  boolean
     */
    public function getEnable(): bool {
        return $this->enable;
    }

    /**
     * @param  boolean  $enable
     * @return  SetMenu
     */
    public function setEnable(bool $enable) {
        $this->enable = $enable;
        return $this;
    }
}
