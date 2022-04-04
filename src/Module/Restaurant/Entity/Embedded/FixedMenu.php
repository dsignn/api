<?php

declare(strict_types=1);

namespace App\Module\Restaurant\Entity\Embedded;

use App\Storage\Entity\Embedded\Price\PriceAwareInterface;
use App\Storage\Entity\Embedded\Price\PriceAwareInterfaceTrait;

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
     * @return string
     */
    protected $note;

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

    /**
      * @return  string|null
     */ 
    public function getNote() {
        return $this->note;
    }

    /**
     * @return  FixedMenu
     */ 
    public function setNote($note) {
        $this->note = $note;
        return $this;
    }
}
