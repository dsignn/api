<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Adapter\StorageAdapterInterface;
use App\Storage\Entity\EntityInterface;
use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Hydrator\HydratorAwareTrait;

/**
 * Class Storage
 * @package App\Storage
 */
class Storage implements StorageInterface {

    /**
     * @var string
     */
    static public $BEFORE_SAVE = 'before_save';

    /**
     * @var string
     */
    static public $AFTER_SAVE = 'after_save';

    use ObjectPrototypeTrait, HydratorAwareTrait;

    /**
     * @var StorageAdapterInterface
     */
    protected $storage;

    /**
     * @var EventManager
     */
    protected $events;

    /**
     * Storage constructor.
     * @param StorageAdapterInterface $storage
     */
    public function __construct(StorageAdapterInterface $storage) {
        $this->storage = $storage;
        $this->events = new EventManager();
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

        $this->events->trigger(
            Storage::$BEFORE_SAVE,
            $entity
        );

        $dataSave = $this->storage->save($this->hydrator->extract($entity));

        $this->hydrator->hydrate($dataSave, $entity);

        $this->events->trigger(
            Storage::$AFTER_SAVE,
            $entity
        );
        
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function update(EntityInterface $entity): EntityInterface {
        // TODO event pre update
        $dataToUpdate = $this->storage->update($this->hydrator->extract($entity));
        // TODO event post update
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id): bool {
        // TODO event pre delete
        return $this->storage->delete($id);
        // TODO event post delete
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = null): ResultSetInterface {
        // TODO event pre getAll
        return $this->storage->getAll($search);
        // TODO event post getAll
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = null): ResultSetPaginateInterface {
        // TODO event pre getPage
        return $this->storage->getPage($page, $itemPerPage, $search);
        // TODO event post getPage
    }

    /**
     * @inheritDoc
     */
    public function generateEntity(array $data): EntityInterface {
        $entity = clone $this->getObjectPrototype();
        $this->getHydrator()->hydrate($data, $entity);
        return $entity;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager() {
        return $this->events;
    }
}
