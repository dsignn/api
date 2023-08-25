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

    use StorageEntityTrait;

    /**
     * @var
     */
    protected $totalMem;
    
    /**
     * @var
     */
    protected $freeMem;

    /**
     * @var
     */
    protected $cpu;

    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $addresses;
}

