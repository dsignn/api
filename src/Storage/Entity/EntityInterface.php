<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Interface EntityInterface
 * @package App\Storage\Entity
 */
interface EntityInterface {

    /**
     * @return string
     */
    public function getId() ;

    /**
     * @param $id
     * @return EntityInterface
     */
    public function setId($id) ;
}