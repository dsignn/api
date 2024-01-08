<?php
declare(strict_types=1);

namespace App\Module\Resource\Entity\Embedded;

/**
 * Class Dimension
 * @package App\Module\Resource\Entity\Embedded
 */
class Dimension {

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * Dimension constructor.
     * @param $width
     * @param $height
     */
    public function __construct($width = 0, $height = 0) {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getHeight(): int {
        return $this->height;
    }

    /**
     * @param int $height
     * @return Size
     */
    public function setHeight(int $height): Dimension {
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
     * @return Size
     */
    public function setWidth(int $width): Dimension {
        $this->width = $width;
        return $this;
    }
}