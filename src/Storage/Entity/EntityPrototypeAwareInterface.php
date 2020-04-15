<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Interface EntityPrototypeAwareInterface
 * @package App\Storage\Entity
 */
interface EntityPrototypeAwareInterface {

    /**
     * @param $data
     * @return EntityPrototypeInterface
     */
    public function getEntityPrototype();

    /**
     * @param EntityPrototypeInterface $entityPrototype
     * @return EntityPrototypeAwareInterface
     */
    public function setEntityPrototype(EntityPrototypeInterface $entityPrototype);
}