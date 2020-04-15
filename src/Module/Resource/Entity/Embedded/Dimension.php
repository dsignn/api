<?php
declare(strict_types=1);

namespace App\Module\Resource\Entity\Embedded;

/**
 * Class Size
 * @package App\Module\Resource\Entity\Embedded
 */
class Size {

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var int
     */
    protected $width = 0;

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
    public function setHeight(int $height): Size {
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
    public function setWidth(int $width): Size {
        $this->width = $width;
        return $this;
    }


}