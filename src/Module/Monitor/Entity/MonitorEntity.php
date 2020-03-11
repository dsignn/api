<?php
declare(strict_types=1);

namespace App\Module\Monitor\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class MonitorEntity
 * @package App\Module\Monitor\Entity
 */
class MonitorEntity implements EntityInterface {

    use StorageEntityTrait;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $description;


    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     * @return MonitorEntity
     */
    public function setName($name): MonitorEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return MonitorEntity
     */
    public function setDescription($description): MonitorEntity {
        $this->description = $description;
        return $this;
    }
}

