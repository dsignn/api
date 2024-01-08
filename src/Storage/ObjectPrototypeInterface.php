<?php
declare(strict_types=1);

namespace App\Storage;

/**
 * Interface ObjectPrototypeInterface
 * @package App\Storage
 */
interface ObjectPrototypeInterface {

    /**
     * @return mixed
     */
    public function getObjectPrototype();

    /**
     * @param $objectPrototype
     * @return mixed
     */
    public function setObjectPrototype($objectPrototype);
}