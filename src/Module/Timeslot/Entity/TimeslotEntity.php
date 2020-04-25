<?php
declare(strict_types=1);

namespace App\Module\Timeslot\Entity;

use App\Module\Monitor\Entity\MonitorContainerEntity;
use App\Module\Monitor\Entity\MonitorReference;
use App\Module\Organization\Entity\OrganizationAwareInterface;
use App\Module\Organization\Entity\OrganizationAwareTrait;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class TimeslotEntity
 * @package App\Module\Timeslot\Entity
 */
class TimeslotEntity implements EntityInterface, OrganizationAwareInterface {

    use StorageEntityTrait, OrganizationAwareTrait;

    /**
     * Status variables
     */
    static public $RUNNING = 'running';
    static public $IDLE = 'idle';
    static public $PAUSE = 'pause';

    /**
     * Rotations variables
     */
    static public $ROTATION_NO = 'rotation-no';
    static public $ROTATION_LOOP = 'rotation-loop';
    static public $ROTATION_INFINITY = 'rotation-infinity';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var int
     */
    protected $duration = 0;

    /**
     * @var string
     */
    protected $rotation = '';

    /**
     * @var bool
     */
    protected $disableAudio = false;

    /**
     * @var array
     */
    protected $binds = [];

    /**
     * @var null
     */
    protected $monitorContainerReference = null;

    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @var null
     */
    protected $options = null;

    /**
     * @var array
     */
    protected $dataReference = [];

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var array
     */
    protected $filters = [];


    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TimeslotEntity
     */
    public function setName(string $name): TimeslotEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @param string $status
     * @return TimeslotEntity
     */
    public function setStatus(string $status): TimeslotEntity {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return TimeslotEntity
     */
    public function setDuration(int $duration): TimeslotEntity {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return string
     */
    public function getRotation(): string {
        return $this->rotation;
    }

    /**
     * @param string $rotation
     * @return TimeslotEntity
     */
    public function setRotation(string $rotation): TimeslotEntity {
        $this->rotation = $rotation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableAudio(): bool {
        return $this->disableAudio;
    }

    /**
     * @param bool $disableAudio
     * @return TimeslotEntity
     */
    public function setDisableAudio(bool $disableAudio): TimeslotEntity {
        $this->disableAudio = $disableAudio;
        return $this;
    }

    /**
     * @return array
     */
    public function getBinds(): array {
        return $this->binds;
    }

    /**
     * @param array $binds
     * @return TimeslotEntity
     */
    public function setBinds(array $binds): TimeslotEntity {
        $this->binds = $binds;
        return $this;
    }

    /**
     * @return MonitorContainerEntity
     */
    public function getMonitorContainerReference() {
        return $this->monitorContainerReference;
    }

    /**
     * @param $monitorContainerReference
     * @return TimeslotEntity
     */
    public function setMonitorContainerReference(MonitorReference $monitorContainerReference): TimeslotEntity {
        $this->monitorContainerReference = $monitorContainerReference;
        return $this;
    }

    /**
     * @return array
     */
    public function getResources(): array {
        return $this->resources;
    }

    /**
     * @param array $resources
     * @return TimeslotEntity
     */
    public function setResources(array $resources): TimeslotEntity {
        $this->resources = $resources;
        return $this;
    }

    /**
     * @return null
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param $options
     * @return TimeslotEntity
     */
    public function setOptions($options): TimeslotEntity {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getDataReference(): array {
        return $this->dataReference;
    }

    /**
     * @param array $dataReference
     * @return TimeslotEntity
     */
    public function setDataReference(array $dataReference): TimeslotEntity {
        $this->dataReference = $dataReference;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return TimeslotEntity
     */
    public function setTags(array $tags): TimeslotEntity {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilters(): array {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return TimeslotEntity
     */
    public function setFilters(array $filters): TimeslotEntity {
        $this->filters = $filters;
        return $this;
    }
}