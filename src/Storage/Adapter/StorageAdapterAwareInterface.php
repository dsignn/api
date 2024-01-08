<?php
declare(strict_types=1);

namespace App\Storage\Adapter;

/**
 * Interface StorageAdapterAwareInterface
 * @package App\Storage\Adapter
 */
interface StorageAdapterAwareInterface {

    /**
     * @return StorageAdapterInterface
     */
    function getStorageAdapter(): StorageAdapterInterface;

    /**
     * @param StorageAdapterInterface $storageAdapter
     * @return StorageAdapterAwareInterface
     */
    function setStorageAdapter(StorageAdapterInterface $storageAdapter);
}