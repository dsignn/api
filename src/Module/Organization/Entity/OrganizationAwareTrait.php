<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity;

use App\Module\Monitor\Entity\MonitorContainerEntity;
use App\Storage\Entity\Reference;

/**
 * Trait OrganizationAwareTrait
 * @package App\Module\Organization\Entity
 */
trait OrganizationAwareTrait {

    protected $organizationReference;

    /**
     * @return Reference|null
     */
    public function getOrganizationReference() {
        return $this->organizationReference;
    }

    /**
     * @param Reference $organizationReference
     * @return MonitorContainerEntity
     */
    public function setOrganizationReference(Reference $organizationReference): MonitorContainerEntity {
        $this->organizationReference = $organizationReference;
        return $this;
    }
}