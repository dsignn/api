<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Adapter\StorageAdapterAwareInterface;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityPrototypeAwareInterface;
use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Laminas\EventManager\EventsCapableInterface;
use Laminas\Hydrator\HydratorAwareInterface;

/**
 * Interface StorageInterface
 * @package App\Storage
 */
interface StorageInterface extends HydratorAwareInterface, EntityPrototypeAwareInterface, EventsCapableInterface, StorageAdapterAwareInterface {

    /**
     * @param $id
     * @return null|EntityInterface
     */
    public function get(string $id);

    /**
     * @param $data
     * @return mixed($data|EntityInterface)
     */
    public function save(EntityInterface &$entity): EntityInterface;

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function update(EntityInterface $entity): EntityInterface;

    /**
     * @param $id
     * @return boolean
     */
    public function delete(string $id): bool;

    /**
     * @param array $search
     * @return ResultSetInterface
     */
    public function getAll(array $search = null): ResultSetInterface;

    /**
     * @param int $page
     * @param int $itemPerPage
     * @param null $search
     * @return ResultSetPaginateInterface
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = null): ResultSetPaginateInterface;
}