<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity;

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

    public function __construct() {
        $this->qrCode = new Reference();
        $this->logo = new Reference();
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
    protected $logo;

    /**
     * @var string
     */
    protected $whatsappPhone = '';

    /**
     * @var bool
     */
    protected $open = false;

    /**
     * @var string
     */
    protected $siteUrl = '';


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
     * @return string
     */
    public function getWhatsappPhone() {
        return $this->whatsappPhone;
    }

    /**
     * @param string $whatsappPhone
     * @return OrganizationEntity
     */
    public function setWhatsappPhone(string $whatsappPhone): OrganizationEntity {
        $this->whatsappPhone = $whatsappPhone;
        return $this;
    }

    /**
     * @return bool
     */
    public function getOpen(): bool {
        return $this->open;
    }

    /**
     * @param bool $open
     * @return OrganizationEntity
     */
    public function setOpen(bool $open): OrganizationEntity {
        $this->open = $open;
        return  $this;
    }

    /**
     * @return string
     */
    public function getSiteUrl(): string {
        return $this->siteUrl;
    }

    /**
     * @param string $siteUrl
     * @return OrganizationEntity
     */
    public function setSiteUrl(string $siteUrl): OrganizationEntity {
        $this->siteUrl = $siteUrl;
        return $this;
    }
}