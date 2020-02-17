<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Entity\EntityInterface;
use Zend\Hydrator\HydratorAwareInterface;
use Zend\Hydrator\HydratorAwareTrait;
use Zend\Hydrator\HydratorInterface;

/**
 * Class Storage
 * @package App\Storage
 */
class Storage implements StorageInterface, ObjectPrototypeInterface, HydratorAwareInterface {

    use ObjectPrototypeTrait;
    use HydratorAwareTrait;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Storage constructor.
     * @param StorageInterface $storage
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(StorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function get($id) {
        $entity = $this->storage->get($id);
        return $this->hydrator ? $this->hydrator->hydrate($entity, clone $this->objectPrototype) : $entity;
    }

    /**
     * @inheritDoc
     */
    public function save(&$data) {

        $dataToSave = $this->hydrator ? $this->hydrator->extract($data) : $data;
        $this->storage->save($dataToSave);
        if ($this->hydrator) {
            $this->hydrator->hydrate($dataToSave, $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function update(&$data) {
        $dataToUpdate = $this->hydrator ? $this->hydrator->extract($data) : $data;
        $this->storage->update($dataToUpdate);
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function delete(EntityInterface $obj) {
        return $this->storage->delete($obj);
    }

    /**
     * @inheritDoc
     */
    public function gelAll(array $search = null) {
        return $this->storage->gelAll($search);
    }
}