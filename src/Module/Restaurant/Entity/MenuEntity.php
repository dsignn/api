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
    static public $STATUS_ENABLE = 'indoor';

    /**
     * @var string
     */
    static public $STATUS_DELIVERY = 'delivery';

    /**
     * @var string
     */
    static public $STATUS_DATE = 'date';

    /**
     * @var string
     */
    static public $STATUS_DISABLE = 'disable';


    /**
     * @var string
     */
    static public $NORMAL_DISH = 0;

    /**
     * @var string
     */
    static public $VEGETARIAN_DISH = 1;

    /**
     * @var string
     */
    static public $VEGAN_DISH = 2;

    /**
     * @var string
     */
    protected $name = '';

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
    protected $backgroundHeader = '';

    /**
     * @var string
     */
    protected $colorHeader = '';

    /**
     * @var string
     */
    protected $note = '';

    /**
     * [$NORMAL_DISH, $VEGETARIAN_DISH, $VEGAN_DISH]
     * @var int
     */
    protected $typeDish = 0;

    /**
     * @var string
     */
    protected $layoutType = 'dsign-menu-item-image';

    /**
     * @var array
     */
    protected $allergens = [];

    /**
     * @var bool
     */
    protected $enableOrder = false;

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var \DateTime
     */
    protected $statusDate = null;

    /**
     * MenuEntity constructor.
     */
    public function __construct() {
        $this->organization = new Reference();
        $this->status = MenuEntity::$STATUS_DISABLE;
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

    /**
     * @return string
     */
    public function getLayoutType(): string {
        return $this->layoutType;
    }

    /**
     * @param string $layoutType
     */
    public function setLayoutType(string $layoutType): void {
        $this->layoutType = $layoutType;
    }

    /**
     * @return string
     */
    public function getNote(): string {
        return $this->note;
    }

    /**
     * @param string $note
     * @return MenuEntity
     */
    public function setNote(string $note): MenuEntity {
        $this->note = $note;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableOrder(): bool {
        return $this->enableOrder;
    }

    /**
     * @param bool $enableOrder
     * @return MenuEntity
     */
    public function setEnableOrder(bool $enableOrder): MenuEntity {
        $this->enableOrder = $enableOrder;
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
     * @return MenuEntity
     */
    public function setStatus(string $status): MenuEntity {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStatusDate() {
        return $this->statusDate;
    }

    /**
     * @param \DateTime $statusDate
     * @return MenuEntity
     */
    public function setStatusDate(\DateTime $statusDate = null): MenuEntity {
        $this->statusDate = $statusDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getTypeDish(): int {
        return $this->typeDish;
    }

    /**
     * @param int $typeDish
     * @return MenuEntity
     */
    public function setTypeDish(int $typeDish = 0): MenuEntity {
        $this->typeDish = $typeDish;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllergens(): array {
        return $this->allergens;
    }

    /**
     * @param array $allergens
     * @return MenuEntity
     */
    public function setAllergens(array $allergens =  []): MenuEntity {
        $this->allergens = $allergens;
        return $this;
    }
}