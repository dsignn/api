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
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Reference
     */
    public function setId(string $id): Reference {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollection(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Reference
     */
    public function SetCollection(string $name): Reference {
        $this->name = $name;
        return $this;
    }
}