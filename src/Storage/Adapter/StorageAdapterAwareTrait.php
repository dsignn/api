<?php
declare(strict_types=1);

namespace App\Storage\Adapter;

/**
 * Interface StorageAdapterAwareInterface
 * @package App\Storage\Adapter
 */
trait StorageAdapterAwareTrait {

    /**
     * @var StorageAdapterInterface
     */
    protected $storage;

    /**
     * @return StorageAdapterInterface
     */
    function getStorageAdapter(): StorageAdapterInterface {
        return $this->storage;
    }

    /**
     * @param StorageAdapterInterface $storageAdapter
     * @return $this
     */
    function setStorageAdapter(StorageAdapterInterface $storageAdapter) {
        $this->storage = $storageAdapter;
        return $this;
    }
}