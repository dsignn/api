<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity;

use App\Module\Monitor\Entity\MonitorContainerEntity;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\Reference;

/**
 * Interface OrganizationAwareInterface
 * @package App\Module\Organization\Entity
 */
interface OrganizationAwareInterface {

    /**
     * @return Reference|null
     */
    public function getOrganizationReference();

    /**
     * @param Reference $organizationReference
     * @return MonitorContainerEntity
     */
    public function setOrganizationReference(Reference $organizationReference = null): EntityInterface;
}