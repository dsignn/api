<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Trait EntityTrait
 * @package App\Storage\Entity
 */
trait EntityTrait {

    protected $id;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param $id
     * @return EntityTrait
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
}