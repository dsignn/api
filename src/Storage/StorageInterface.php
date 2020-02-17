<?php
declare(strict_types=1);

namespace App\Storage;

use App\Storage\Entity\EntityInterface;
use App\Storage\ResultSet\ResultSetInterface;

/**
 * Interface StorageInterface
 * @package App\Storage
 */
interface StorageInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $data
     * @return $data
     */
    public function save(&$data);

    /**
     * @param $data
     * @return $data
     */
    public function update(&$data);

    /**
     * @param EntityInterface $obj
     * @return $data
     */
    public function delete(EntityInterface $obj);

    /**
     * @param array $search
     * @return ResultSetInterface
     */
    public function gelAll(array $search = null);
}