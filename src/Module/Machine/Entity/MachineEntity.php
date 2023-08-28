<?php
declare(strict_types=1);

namespace App\Module\Machine\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class MachineEntity
 * @package App\Module\Machine\Entity
 */
class MachineEntity implements EntityInterface {

    public $id;

    /**
     * @var
     */
    public $totalMem;
    
    /**
     * @var
     */
    public $freeMem;

    /**
     * @var
     */
    public $cpu;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $addresses;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param $id
     * @return EntityTrait
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
}

