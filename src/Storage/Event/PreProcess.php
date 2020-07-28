<?php
declare(strict_types=1);

namespace App\Storage\Event;

use App\Storage\Entity\EntityInterface;

/**
 * Class PreProcess
 * @package App\Storage\Event
 */
class PreProcess {

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var array
     */
    protected $data;

    /**
     * PreProcess constructor.
     * @param EntityInterface $entity
     * @param array $data
     */
    public function __construct(EntityInterface $entity, array $data) {
        $this->entity = $entity;
        $this->data = $data;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }
}