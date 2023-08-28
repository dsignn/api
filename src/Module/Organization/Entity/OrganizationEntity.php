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
    protected $logo;

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
}