<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;

/**
 * Class OrganizationEntity
 * @package App\Module\Organization\Entity
 */
class OrganizationEntity implements EntityInterface {

    use EntityTrait;

    /**
     * @var string
     */
    protected $name = '';

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
}