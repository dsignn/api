<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Adapter\StorageAdapterInterface;
use App\Storage\Entity\EntityInterface;
use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
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
    public function get(string $id) {
        $data = $this->storage->get($id);
        return $this->hydrator && $data ? $this->hydrator->hydrate($data, clone $this->objectPrototype) : $data;
    }

    /**
     * @inheritDoc
     */
    public function save(EntityInterface &$entity): EntityInterface {
        $this->hydrator->hydrate(
            $this->storage->save($this->hydrator->extract($entity)),
            $entity
        );

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function update(EntityInterface $entity): EntityInterface {

        // TODO evt
        $dataToUpdate = $this->storage->update($this->hydrator->extract($entity));
        // TODO evt
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id): bool {
        return $this->storage->delete($id);
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = null): ResultSetInterface {
        return $this->storage->getAll($search);
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = null): ResultSetPaginateInterface {
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
