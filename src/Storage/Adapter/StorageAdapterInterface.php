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
     * @return
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