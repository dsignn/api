<?php
declare(strict_types=1);

namespace App\Module\Device\Entity;

use App\Storage\Entity\Reference;

/**
 * Class MonitorContainerEntityReference
 * @package App\Module\Monitor\Entity
 */
class MonitorContainerEntityReference extends Reference {

    /**
     * @var
     */
    protected $monitors = [];

    /**
     * @return mixed
     */
    public function getMonitors() {
        return $this->monitors;
    }

    /**
     * @param string $parentId
     * @return MonitorContainerEntityReference
     */
    public function setMonitors(array $monitors = []): MonitorContainerEntityReference {
        $this->monitors = $monitors;
        return $this;
    }
}