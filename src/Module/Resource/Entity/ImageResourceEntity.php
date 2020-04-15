<?php
declare(strict_types=1);

namespace App\Module\Resource\Entity;

use App\Module\Resource\Entity\Embedded\Dimension;

/**
 * Class ImageResourceEntity
 * @package App\Module\Resource\Entity
 */
class ImageResourceEntity extends AbstractResourceEntity {

    /**
     * @var Dimension|null
     */
    protected $dimension;

    /**
     * @return Dimension|null
     */
    public function getDimension(): ?Dimension {
        return $this->dimension;
    }

    /**
     * @param Dimension|null $dimension
     * @return ImageResourceEntity
     */
    public function setDimension(?Dimension $dimension): ImageResourceEntity {
        $this->dimension = $dimension;
        return $this;
    }
}