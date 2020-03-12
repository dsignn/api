<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\ResultSet\ResultSetInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;

/**
 * Interface StorageInterface
 * @package App\Storage
 */
interface StorageInterface {
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $data
     * @return mixed($data|EntityInterface)
     */
    public function save($data);

    /**
     * @param $data
     * @return  mixed($data|EntityInterface)
     */
    public function update($data);

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