<?php
declare(strict_types=1);

namespace App\Storage\Adapter;

use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;

/**
 * Interface StorageAdapterInterface
 * @package App\Storage
 */
interface StorageAdapterInterface {
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param array $data
     * @return array
     */
    public function save(array $data): array ;

    /**
     * @param array $data
     * @return  array
     */
    public function update(array $data): array ;

    /**
     * @param $id
     * @return boolean
     */
    public function delete($id): bool;

    /**
     * @param array $search
     * @param array $order
     * @return ResultSetInterface
     */
    public function getAll(array $search = [], array $order = []): ResultSetInterface;

    /**
     * @param int $page
     * @param int $itemPerPage
     * @param array $search
     * @param array $order
     * @return ResultSetPaginateInterface
     */
    public function getPage($page = 1, $itemPerPage = 10, array $search = [], array $order = []): ResultSetPaginateInterface;
}