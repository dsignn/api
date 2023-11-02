<?php
declare(strict_types=1);

namespace App\Module\Device\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\Embedded\Date\DateAwareInterface;
use App\Storage\Entity\Embedded\Date\DateAwareInterfaceTrait;

/**
 * Class DeviceEntity
 * @package App\Module\Device\Entity
 */
class DeviceEntity implements EntityInterface, DateAwareInterface {

    use DateAwareInterfaceTrait;

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

    public $createdDate;

    public $lastUpdateDate;

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

