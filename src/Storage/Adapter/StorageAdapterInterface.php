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
    public function save(array $data);

    /**
     * @param array $data
     * @return  array
     */
    public function update(array $data);

    /**
     * @param $id
     * @return boolean
     */
    public function delete($id);

    /**
     * @param array $search
     * @return ResultSetInterface
     */
    public function getAll(array $search = null);

    /**
     * @param int $page
     * @param int $itemPerPage
     * @param null $search
     * @return ResultSetPaginateInterface
     */
    public function getPage($page = 1, $itemPerPage = 10, $search = null);
}