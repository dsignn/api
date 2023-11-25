<?php
declare(strict_types=1);

namespace App\Module\Playlist\Entity;

use App\Module\Organization\Entity\OrganizationAwareInterface;
use App\Module\Organization\Entity\OrganizationAwareTrait;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class MonitorEntity
 * @package App\Module\Monitor\Entity
 */
class PlaylistEntity implements EntityInterface, OrganizationAwareInterface {

    use StorageEntityTrait, OrganizationAwareTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $resources;

    /**
     * @var [type]
     */
    protected $monitorContainerReference;

    /**
     * @var array
     */
    protected $binds = [];

   
    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     * @return PlaylistEntity
     */
    public function setName($name): PlaylistEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of monitorContainerReference
     *
     * @return  [type]
     */ 
    public function getMonitorContainerReference() {
        return $this->monitorContainerReference;
    }

    /**
     * Set the value of monitorContainerReference
     *
     * @param  [type]  $monitorContainerReference
     *
     * @return  self
     */ 
    public function setMonitorContainerReference( $monitorContainerReference) {
        $this->monitorContainerReference = $monitorContainerReference;
        return $this;
    }

    /**
     * Get the value of resources
     *
     * @return  array
     */ 
    public function getResources() {
        return $this->resources;
    }

    /**
     * Set the value of resources
     *
     * @param  array  $resources
     *
     * @return  self
     */ 
    public function setResources(array $resources) {
        $this->resources = $resources;
        return $this;
    }

    /**
     * @return  array
     */ 
    public function getBinds() {
        return $this->binds;
    }

    /**
     * @param  array  $binds
     * @return  self
     */ 
    public function setBinds(array $binds) {
        $this->binds = $binds;
        return $this;
    }
}

