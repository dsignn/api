<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Class Reference
 * @package App\Storage\Entity
 */
class Reference implements ReferenceInterface {

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $collection = '';

    /**
     * Reference constructor.
     * @param string $id
     * @param string $collection
     */
    public function __construct(string $id = '', string $collection = '') {
        $this->id = $id;
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Reference
     */
    public function setId(string $id): ReferenceInterface {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollection(): string {
        return $this->collection;
    }

    /**
     * @param string $collection
     * @return Reference
     */
    public function SetCollection(string $collection): ReferenceInterface {
        $this->collection = $collection;
        return $this;
    }
}