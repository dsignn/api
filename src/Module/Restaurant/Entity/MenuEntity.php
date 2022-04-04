<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Entity;

use App\Module\Restaurant\Entity\Embedded\FixedMenu;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\Embedded\SetMenu;
use App\Storage\Entity\Embedded\Price\Price;
use App\Storage\Entity\Embedded\Price\PriceInterface;
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
    const STATUS_DISABLE = 'disable';


    /**
     * @var string
     */
    const STATUS_ENABLE = 'enable';


    /**
     * @var string
     */
    const TYPE_INDOOR = 'indoor';

    /**
     * @var string
     */
    const TYPE_DELIVERY = 'delivery';

    /**
     * @var string
     */
    const TYPE_DAILY = 'daily';

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
    protected $layoutType = 'dsign-menu-item-image';

    /**
     * @var bool
     */
    protected $enableOrder = false;

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var \DateTime
     */
    protected $statusDate = null;

    /**
     * @var FixedMenu
     */
    protected $fixedMenu;

    /**
     * MenuEntity constructor.
     */
    public function __construct() {
        $this->organization = new Reference();
        $this->status = MenuEntity::STATUS_DISABLE;
        $this->type = MenuEntity::TYPE_INDOOR;
        $this->fixedMenu = new FixedMenu();
        $this->fixedMenu->setPrice(new Price());
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
     * @return \DateTime
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $statusDate
     * @return MenuEntity
     */
    public function setType(string $type = null): MenuEntity {
        $this->type = $type;
        return $this;
    }

    /**
     * @return  FixedMenu
     */ 
    public function getFixedMenu(): FixedMenu {
        return $this->fixedMenu;
    }

    /**
     * @param  FixedMenu $fixedMenu
     * @return  MenuEntity
     */ 
    public function setFixedMenu(FixedMenu $fixedMenu) {
        $this->fixedMenu = $fixedMenu;
        return $this;
    }
}