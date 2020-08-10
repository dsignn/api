<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Entity;

use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;
use App\Storage\Entity\Reference;
use App\Storage\Entity\ReferenceInterface;

/**
 * Class MenuEntity
 * @package App\Module\Restaurant\Entity
 */
class MenuEntity implements EntityInterface
{

    use EntityTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<MenuItem>
     */
    protected $items = [];

    /**
     * @var ReferenceInterface
     */
    protected $organization;

    /**
     * @var string
     */
    protected $backgroundHeader;

    /**
     * @var string
     */
    protected $colorHeader;

    /**
     * MenuEntity constructor.
     */
    public function __construct() {
        $this->organization = new Reference();
    }

    /**
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @param array $items
     * @return MenuEntity
     */
    public function setItems(array $items): MenuEntity {
        $this->items = $items;
        return $this;
    }

    /**
     * @param MenuItem $item
     * @return $this
     */
    public function appendItem(MenuItem $item) {
        array_push($this->items, $item);
        return $this;
    }

    /**
     * @return ReferenceInterface
     */
    public function getOrganization(): ReferenceInterface {
        return $this->organization;
    }

    /**
     * @param ReferenceInterface $organization
     * @return MenuEntity
     */
    public function setOrganization(ReferenceInterface $organization): MenuEntity {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return MenuEntity
     */
    public function setName(string $name): MenuEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundHeader(): string {
        return $this->backgroundHeader;
    }

    /**
     * @param string $backgroundHeader
     * @return MenuEntity
     */
    public function setBackgroundHeader(string $backgroundHeader): MenuEntity {
        $this->backgroundHeader = $backgroundHeader;
        return $this;
    }

    /**
     * @return string
     */
    public function getColorHeader(): string {
        return $this->colorHeader;
    }

    /**
     * @param string $colorHeader
     * @return MenuEntity
     */
    public function setColorHeader(string $colorHeader): MenuEntity {
        $this->colorHeader = $colorHeader;
        return $this;
    }
}