<?php
declare(strict_types=1);

namespace App\Module\Monitor\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class MonitorContainerEntity
 * @package App\Module\Monitor\Entity
 */
class MonitorContainerEntity implements EntityInterface {

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
     * @var
     */
    protected $monitors = [];


    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     * @return MonitorContainerEntity
     */
    public function setName($name): MonitorContainerEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getMonitors() {
        return $this->monitors;
    }

    /**
     * @param array $monitors
     * @return MonitorContainerEntity
     */
    public function setMonitors(array $monitors): MonitorContainerEntity {
        $this->monitors = $monitors;
        return $this;
    }

    /**
     * @param MonitorEntity $monitorEntity
     * @return MonitorContainerEntity
     */
    public function attachMonitor(MonitorEntity $monitorEntity): MonitorContainerEntity {
        array_push($this->monitors, $monitorEntity);
        return $this;
    }

    /**
     * @param MonitorEntity $monitorEntity
     * @return MonitorContainerEntity
     */
    public function removeMonitor(MonitorEntity $monitorEntity): MonitorContainerEntity {
        $cont = 0;
        /** @var MonitorEntity $monitor */
        foreach ($this->monitors as $monitor) {
            if ($monitor->getId() === $monitorEntity->getId()) {
                array_splice($this->monitors, $cont, 1);
                break;
            }
            $cont++;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     * @return MonitorContainerEntity
     */
    public function setDescription($description): MonitorContainerEntity {
        $this->description = $description;
        return $this;
    }
}

