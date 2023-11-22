<?php
declare(strict_types=1);

namespace App\Module\Resource\Entity;

use App\Module\Resource\Entity\Embedded\Dimension;

/**
 * Class AudioResourceEntity
 * @package App\Module\Resource\Entity
 */
class AudioResourceEntity extends AbstractResourceEntity {

    /**
     * @var int
     */
    protected $duration = 0;

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
    public function setDuration(float $duration): AudioResourceEntity {
        $this->duration = $duration;
        return $this;
    }
}