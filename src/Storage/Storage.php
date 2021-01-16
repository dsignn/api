<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Adapter\StorageAdapterAwareTrait;
use App\Storage\Adapter\StorageAdapterInterface;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityPrototypeAwareTrait;
use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Hydrator\HydratorAwareTrait;
use function DI\value;

/**
 * Class Storage
 * @package App\Storage
 */
class Storage implements StorageInterface {

    /**
     * Traits
     */
    use EntityPrototypeAwareTrait, HydratorAwareTrait, StorageAdapterAwareTrait;

    /**
     * @var string
     */
    static public $BEFORE_SAVE = 'before_save';

    /**
     * @var string
     */
    static public $AFTER_SAVE = 'after_save';

    /**
     * @var string
     */
    static public $BEFORE_UPDATE = 'before_update';

    /**
     * @var string
     */
    static public $AFTER_UPDATE = 'after_update';

    /**
     * @var string
     */
    static public $BEFORE_DELETE = 'before_delete';

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
        return $this->hydrator && $data ?
            $this->hydrator->hydrate($data, clone $this->getEntityPrototype()->getPrototype($data)) :
            $data;
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

        $this->events->trigger(
            Storage::$BEFORE_UPDATE,
            $entity
        );

        $dataToUpdate = $this->storage->update($this->hydrator->extract($entity));
        $this->hydrator->hydrate($dataToUpdate, $entity);

        $this->events->trigger(
            Storage::$AFTER_UPDATE,
            $entity
        );

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id): bool {

        $entity = $this->get($id);
        if (!$entity) {
            return false;
        }

        $this->events->trigger(
            Storage::$BEFORE_DELETE,
            $entity
        );

        return $this->storage->delete($id);
        // TODO event post delete
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $search = null, array $order = []): ResultSetInterface {
        // TODO event pre getAll
        return $this->storage->getAll($search, $order);
        // TODO event post getAll
    }

    /**
     * @inheritDoc
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = [], $order = []): ResultSetPaginateInterface {
        // TODO event pre getPage
        return $this->storage->getPage($page, $itemPerPage, $search, $order);
        // TODO event post getPage
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager() {
        return $this->events;
    }
}
