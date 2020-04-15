<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Class SingleEntityPrototype
 * @package App\Storage\Entity
 */
class SingleEntityPrototype implements EntityPrototypeInterface {

    /**
     * @var
     */
    protected $prototype;

    /**
     * SingleEntityPrototype constructor.
     * @param $prototype
     * @throws \Exception
     */
    public function __construct($prototype) {
        if (!is_object($prototype)) {
            throw new \Exception('prototype must be and object');
        }
        $this->prototype = $prototype;
    }

    /**
     * @inheritDoc
     */
    public function getPrototype($data = null) {
        return clone $this->prototype;
    }
}