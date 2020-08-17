<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Event;

use App\Module\Restaurant\Entity\MenuEntity;
use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\StorageInterface;
use Laminas\EventManager\EventInterface;
use MongoDB\BSON\ObjectId;

/**
 * Class DisableMenu
 * @package App\Module\Restaurant\Event
 */
class DisableMenu {

    protected $storage;

    /**
     * DisableMenu constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param EventInterface $event
     * @throws \Exception
     */
    public function __invoke(EventInterface $event) {

        $adapter = $this->storage->getStorageAdapter();
        /** @var MenuEntity $entity */
        $entity = $event->getTarget();

        if ($adapter instanceof MongoAdapter && $entity->getEnable()) {
            /** @var \MongoCollection $collection */
            $collection = $adapter->getCollection();

            $cond = [
                "organization._id" => new ObjectId($entity->getOrganization()->getId()),
                "_id" => ['$ne' => new ObjectId($entity->getId())]
            ];

            $collection->updateMany(
                $cond,
                ['$set' => ["enable" => false]]
            );
        }
    }
}