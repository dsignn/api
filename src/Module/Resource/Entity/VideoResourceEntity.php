<?php
declare(strict_types=1);

namespace App\Module\Resource\Entity;

use App\Module\Resource\Entity\Embedded\Dimension;

/**
 * Class VideoResourceEntity
 * @package App\Module\Resource\Entity
 */
class VideoResourceEntity extends AbstractResourceEntity {

    /**
     * @var int
     */
    protected $duration = 0;

    /**
     * @var Dimension|null
     */
    protected $dimension;

    /**
     * @var string
     */
    protected $aspectRatio;

    /**
     * @return int
     */
    public function getDuration(): float {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return VideoResourceEntity
     */
    public function setDuration(float $duration): VideoResourceEntity {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return Dimension|null
     */
    public function getDimension(): ?Dimension {
        return $this->dimension;
    }

    /**
     * @param Dimension|null $dimension
     * @return VideoResourceEntity
     */
    public function setDimension(?Dimension $dimension): VideoResourceEntity {
        $this->dimension = $dimension;
        return $this;
    }

    /**
     * @return string
     */
    public function getAspectRatio(): string {
        return $this->aspectRatio;
    }

    /**
     * @param string $aspectRatio
     * @return VideoResourceEntity
     */
    public function setAspectRatio(string $aspectRatio): VideoResourceEntity {
        $this->aspectRatio = $aspectRatio;
        return $this;
    }
}