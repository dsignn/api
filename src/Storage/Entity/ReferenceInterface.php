<?php
declare(strict_types=1);

namespace App\Storage\Entity;

/**
 * Interface ReferenceInterface
 * @package App\Storage\Entity
 */
interface ReferenceInterface {

    /**
     * @return string
     */
    public function getId(): string ;


    /**
     * @return string
     */
    public function getCollection(): string ;


    /**
     * @param string $id
     * @return ReferenceInterface
     */
    public function setId(string $id): ReferenceInterface ;


    /**
     * @param string $collection
     * @return ReferenceInterface
     */
    public function setCollection(string $collection): ReferenceInterface ;

}