<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Interface EntityPrototypeAwareInterface
 * @package App\Storage\Entity
 */
trait EntityPrototypeAwareTrait {

    /**
     * @var
     */
    protected $entityPrototype;

    /**
     * @return EntityPrototypeInterface|null
     */
    public function getEntityPrototype() {

        return $this->entityPrototype;
    }

    /**
     * @param EntityPrototypeInterface $entityPrototype
     * @return EntityPrototypeAwareTrait
     */
    public function setEntityPrototype(EntityPrototypeInterface $entityPrototype) {

        $this->entityPrototype = $entityPrototype;
        return $this;
    }
}