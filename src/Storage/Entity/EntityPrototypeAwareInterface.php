<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Interface EntityInterface
 * @package App\Storage\Entity
 */
interface EntityPrototypeInterface {

    /**
     * @param $data
     * @return mixed
     */
    public function getEntityPrototype($data = null);
}