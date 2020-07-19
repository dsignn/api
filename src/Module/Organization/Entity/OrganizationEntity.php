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
}