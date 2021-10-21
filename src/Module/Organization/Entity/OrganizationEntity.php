<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity;

use App\Module\Organization\Entity\Embedded\Address\Address;
use App\Module\Organization\Entity\Embedded\Phone\Phone;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;
use App\Storage\Entity\Reference;
use App\Storage\Entity\ReferenceInterface;


/**
 * Class OrganizationEntity
 * @package App\Module\Organization\Entity
 */
class OrganizationEntity implements EntityInterface {

    use EntityTrait;

    /**
     * OrganizationEntity constructor.
     */
    public function __construct() {

        $this->qrCode = new Reference();
        $this->qrCodeDelivery = new Reference();
        $this->logo = new Reference();
        $this->whatsappPhone = new Phone();
        $this->address = new Address();
    }

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $normalizeName = '';

    /**
     * @var ReferenceInterface
     */
    protected $qrCode;

    /**
     * @var ReferenceInterface
     */
    protected $qrCodeDelivery;

    /**
     * @var ReferenceInterface
     */
    protected $logo;

    /**
     * @var string
     */
    protected $whatsappPhone;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var string
     */
    protected $siteUrl = '';

    /**
     * @var int
     */
    protected $tableNumber = 0;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return OrganizationEntity
     */
    public function setName(string $name): OrganizationEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNormalizeName(): string {
        return $this->normalizeName;
    }

    /**
     * @param string $normalizeName
     * @return OrganizationEntity
     */
    public function setNormalizeName(string $normalizeName): OrganizationEntity {
        $this->normalizeName = $normalizeName;
        return $this;
    }

    /**
     * @return ReferenceInterface
     */
    public function getQrCode(): ReferenceInterface {
        return $this->qrCode;
    }

    /**
     * @param ReferenceInterface $qrCode
     * @return OrganizationEntity
     */
    public function setQrCode(ReferenceInterface $qrCode): OrganizationEntity {
        $this->qrCode = $qrCode;
        return $this;
    }

    /**
     * @return ReferenceInterface
     */
    public function getQrCodeDelivery(): ReferenceInterface {
        return $this->qrCodeDelivery;
    }

    /**
     * @param ReferenceInterface $qrCodeDelivery
     * @return OrganizationEntity
     */
    public function setQrCodeDelivery(ReferenceInterface $qrCodeDelivery): OrganizationEntity {
        $this->qrCodeDelivery = $qrCodeDelivery;
        return $this;
    }

    /**
     * @return ReferenceInterface
     */
    public function getLogo(): ReferenceInterface {
        return $this->logo;
    }

    /**
     * @param ReferenceInterface $logo
     * @return OrganizationEntity
     */
    public function setLogo(ReferenceInterface $logo): OrganizationEntity {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return Phone
     */
    public function getWhatsappPhone(): Phone {
        return $this->whatsappPhone;
    }

    /**
     * @param string $whatsappPhone
     * @return OrganizationEntity
     */
    public function setWhatsappPhone(Phone $whatsappPhone): OrganizationEntity {
        $this->whatsappPhone = $whatsappPhone;
        return $this;
    }

    /**
     * @return string
     */
    public function getSiteUrl() {
        return $this->siteUrl;
    }

    /**
     * @param string $siteUrl
     * @return OrganizationEntity
     */
    public function setSiteUrl(string $siteUrl = null): OrganizationEntity {
        $this->siteUrl = $siteUrl;
        return $this;
    }

    /**
     * @return int
     */
    public function getTableNumber(): int {
        return $this->tableNumber;
    }

    /**
     * @param int $tableNumber
     * @return OrganizationEntity
     */
    public function setTableNumber(int $tableNumber = 0): OrganizationEntity {
        $this->tableNumber = $tableNumber;
        return $this;
    }

    /**
     * Get the value of address
     *
     * @return  Address
     */ 
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @param  Address  $address
     * @return  self
     */ 
    public function setAddress(Address $address) {
        $this->address = $address;
        return $this;
    }
}