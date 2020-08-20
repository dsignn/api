<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Entity\Embedded;

use App\Module\Restaurant\Entity\Embedded\Price\Price;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;
use App\Storage\Entity\ReferenceInterface;

/**
 * Class MenuItem
 * @package App\Module\Restaurant\Entity\Embedded
 */
class MenuItem implements EntityInterface {

    use EntityTrait;

    const STATUS_AVAILABLE = 'available';
    const STATUS_OVER = 'over';
    const STATUS_NOT_AVAILABLE = 'not-available';

    /**
     * @var array
     */
    protected $name = [];

    /**
     * @var array
     */
    protected $description = [];

    /**
     * @var string
     */
    protected $category = '';

    /**
     * @var string
     */
    protected $price;

    /**
     * @var int
     */
    protected $new = 0;

    /**
     * @var
     */
    protected $status = MenuItem::STATUS_AVAILABLE;

    /**
     * @var array<ReferenceInterface>
     */
    protected $photos = [];

    /**
     * MenuItem constructor.
     */
    public function __construct() {
        $this->price = new Price();
    }

    /**
     * @return array
     */
    public function getName(): array {
        return $this->name;
    }

    /**
     * @param string $name
     * @return MenuItem
     */
    public function setName(array $name): MenuItem {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getDescription(): array {
        return $this->description;
    }

    /**
     * @param string $description
     * @return MenuItem
     */
    public function setDescription(array $description): MenuItem {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string {
        return $this->category;
    }

    /**
     * @param string $category
     * @return MenuItem
     */
    public function setCategory(string $category): MenuItem {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price {
        return $this->price;
    }

    /**
     * @param Price $price
     * @return MenuItem
     */
    public function setPrice(Price $price): MenuItem {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getNew(): int {
        return $this->new;
    }

    /**
     * @param int $new
     * @return MenuItem
     */
    public function setNew(int $new): MenuItem {
        $this->new = $new;
        return $this;
    }

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }

    /**
     * @param array $photos
     * @return MenuItem
     */
    public function setPhotos(array $photos): MenuItem {
        $this->photos = $photos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param $status
     * @return MenuItem
     */
    public function setStatus($status): MenuItem {
        $this->status = $status;
        return  $this;
    }
}