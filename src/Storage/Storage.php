<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Adapter\StorageAdapterInterface;
use App\Storage\Entity\EntityInterface;
use Laminas\Hydrator\HydratorAwareTrait;

/**
 * Class Storage
 * @package App\Storage
 */
class Storage implements StorageInterface {

    use ObjectPrototypeTrait;
    use HydratorAwareTrait;

    /**
     * @var StorageAdapterInterface
     */
    protected $storage;

    /**
     * Storage constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageAdapterInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function get($id) {
        $data = $this->storage->get($id);
        return $this->hydrator && $data ? $this->hydrator->hydrate($data, clone $this->objectPrototype) : $data;
    }

    /**
     * @inheritDoc
     */
    public function save($data) {

        if ($this->hydrator && $data instanceof EntityInterface === true) {
            $dataSave = $this->hydrator->extract($data);
        } else {
            $dataSave = $data;
        }

        $dataSave = $this->storage->save($dataSave);

        if ($this->hydrator) {
            $dataSave = $this->hydrator->hydrate($dataSave, clone $this->objectPrototype);
        }
        return $dataSave;
    }

    /**
     * @inheritDoc
     */
    public function update($data) {

        if ($this->hydrator && $data instanceof EntityInterface === true) {
            $dataToUpdate = $this->hydrator->extract($data);
        } else {
            $dataToUpdate = $data;
        }

        $dataToUpdate = $this->storage->update($dataToUpdate);

        if ($this->hydrator && $data instanceof EntityInterface !== true) {
            $dataToUpdate = $this->hydrator->hydrate($dataToUpdate, clone $this->objectPrototype);
        }

        return $dataToUpdate;
    }

    /**
     * @inheritDoc
     */
    public function delete($id) {
        return $this->storage->delete($id);
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = null) {
        return $this->storage->getAll($search);
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = null) {
        return $this->storage->getPage($page, $itemPerPage, $search);
    }

    /**
     * @inheritDoc
     */
    public function generateEntity(array $data): EntityInterface {
        $entity = clone $this->getObjectPrototype();
        $this->getHydrator()->hydrate($data, $entity);
        return $entity;
    }
}
