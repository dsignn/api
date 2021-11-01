<?php
declare(strict_types=1);

namespace App\Module\Order\Entity;

use App\Module\Organization\Entity\Embedded\Phone\Phone;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;
use App\Storage\Entity\Reference;
use App\Storage\Entity\ReferenceInterface;
use DateTime;

/**
 * Class OrderEntity
 * @package App\Module\Order\Entity
 */
class OrderEntity implements EntityInterface {

       
    /**
     * @var string
     */
    const STATUS_CHECK = 'check';

    /**
     * @var string
     */
    const STATUS_QUEUE = 'queue';
   
    /**
     * @var string
     */
    const STATUS_PREPARATION = 'preparation';
   
    /**
     * @var string
     */
    const STATUS_DELIVERING = 'delivering';

        /**
     * @var string
     */
    const STATUS_DELIVERED = 'delivered';

    /**
     * @var string
     */
    const STATUS_INVALID = 'invalid';

    /**
     * Traits
     */
    use EntityTrait;

    /**
     * @var ReferenceInterface
     */
    protected $organization;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var DateTime
     */
    protected $createdAt = null;

    /**
     * @var DateTime
     */
    protected $lastUpdateAt = null;

    /**
     * @var string
     */
    protected $status;

    /**
     * OrderEntity constructor.
     */
    public function __construct() {
        $this->status = self::STATUS_CHECK;
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
    public function setOrganization(ReferenceInterface $organization): OrderEntity {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */ 
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  string  $status
     * @return  self
     */ 
    public function setStatus(string $status): OrderEntity {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the value of lastUpdateAt
     * @return  DateTime
     */ 
    public function getLastUpdateAt() {
        return $this->lastUpdateAt;
    }

    /**
     * Set the value of lastUpdateAt
     *
     * @param  DateTime  $lastUpdateAt
     * @return  self
     */ 
    public function setLastUpdateAt(DateTime $lastUpdateAt = null): OrderEntity{
        $this->lastUpdateAt = $lastUpdateAt;
        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return  DateTime
     */ 
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  DateTime  $createdAt
     * @return  self
     */ 
    public function setCreatedAt(DateTime $createdAt = null): OrderEntity {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get the value of orders
     *
     * @return  array
     */ 
    public function getItems() {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @param  array  $items
     * @return  self
     */ 
    public function setItems(array $items): OrderEntity {
        $this->items = $items;
        return $this;
    }
}
