<?php
declare(strict_types=1);

namespace App\Module\Device\Entity;

use App\Storage\Entity\Reference;
use App\Storage\Entity\ReferenceInterface;

/**
 * Class MonitorEntityReference
 * @package App\Module\Monitor\Entity
 */
class MonitorEntityReference extends Reference {

    /**
     * @var array
     */
    protected $monitors = [];

    /**
     * @var ReferenceInterface
     */
    protected $playlist;

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
    public function setMonitors(array $monitors = []): MonitorEntityReference {
        $this->monitors = $monitors;
        return $this;
    }

    /**
     * @return  ReferenceInterface
     */ 
    public function getPlaylist() {
        return $this->playlist;
    }

    /**
     * @param  ReferenceInterface  $playlist
     */ 
    public function setPlaylist(ReferenceInterface $playlist = null) {
        $this->playlist = $playlist;
        return $this;
    }
}