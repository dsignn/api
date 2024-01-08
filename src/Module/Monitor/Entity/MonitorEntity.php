<?php
declare(strict_types=1);

namespace App\Module\Monitor\Entity;

use App\Module\Organization\Entity\OrganizationAwareInterface;
use App\Module\Organization\Entity\OrganizationAwareTrait;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class MonitorEntity
 * @package App\Module\Monitor\Entity
 */
class MonitorEntity implements EntityInterface, OrganizationAwareInterface {

    use StorageEntityTrait;
    use OrganizationAwareTrait;

    /**
     * @var
     */
    protected $name;

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $offsetX = 0;

    /**
     * @var int
     */
    protected $offsetY = 0;

    /**
     * @var bool
     */
    protected $alwaysOnTop = false;

    /**
     * @var string
     */
    protected $backgroundColor = 'transparent';

    /**
     * @var array
     */
    protected $polygonPoints = [];

    /**
     * @var
     */
    protected $monitors = [];

    /**
     * @var \stdClass
     */
    protected $defaultResourceReference;


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
     * @return int
     */
    public function getHeight(): int {
        return $this->height;
    }

    /**
     * @param int $height
     * @return MonitorEntity
     */
    public function setHeight(int $height): MonitorEntity {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int {
        return $this->width;
    }

    /**
     * @param int $width
     * @return MonitorEntity
     */
    public function setWidth(int $width): MonitorEntity {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffsetX(): int {
        return $this->offsetX;
    }

    /**
     * @param int $offsetX
     * @return MonitorEntity
     */
    public function setOffsetX(int $offsetX): MonitorEntity {
        $this->offsetX = $offsetX;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffsetY(): int {
        return $this->offsetY;
    }

    /**
     * @param int $offsetY
     * @return MonitorEntity
     */
    public function setOffsetY(int $offsetY): MonitorEntity {
        $this->offsetY = $offsetY;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAlwaysOnTop(): bool {
        return $this->alwaysOnTop;
    }

    /**
     * @param bool $alwaysOnTop
     * @return MonitorEntity
     */
    public function setAlwaysOnTop(bool $alwaysOnTop): MonitorEntity {
        $this->alwaysOnTop = $alwaysOnTop;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): string {
        return $this->backgroundColor;
    }

    /**
     * @param string $backgroundColor
     * @return MonitorEntity
     */
    public function setBackgroundColor(string $backgroundColor): MonitorEntity {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * @return array
     */
    public function getPolygonPoints(): array {
        return $this->polygonPoints;
    }

    /**
     * @param array $polygonPoints
     * @return MonitorEntity
     */
    public function setPolygonPoints(array $polygonPoints): MonitorEntity {
        $this->polygonPoints = $polygonPoints;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultResourceReference() {
        return $this->defaultResourceReference;
    }

    /**
     * @param $defaultResourceReference
     * @return MonitorEntity
     */
    public function setDefaultResourceReference($defaultResourceReference): MonitorEntity {
        $this->defaultResourceReference = $defaultResourceReference;
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
     * @return MonitorEntity
     */
    public function setMonitors(array $monitors): MonitorEntity {
        $this->monitors = $monitors;
        return $this;
    }
}

