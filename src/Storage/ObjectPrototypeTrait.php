<?php
declare(strict_types=1);

namespace App\Storage;

/**
 * Interface ObjectPrototypeInterface
 * @package App\Storage
 */
trait ObjectPrototypeTrait {

    /**
     * @var
     */
    protected $objectPrototype;

    /**
     * @return mixed
     */
    public function getObjectPrototype()
    {
        return $this->objectPrototype ? $this->objectPrototype : new \stdClass();
    }

    /**
     * @param $objectPrototype
     * @return self
     */
    public function setObjectPrototype($objectPrototype): self
    {
        $this->objectPrototype = $objectPrototype;
        return $this;
    }
}