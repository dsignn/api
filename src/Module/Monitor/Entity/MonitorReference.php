<?php
declare(strict_types=1);

namespace App\Module\Monitor\Entity;

use App\Storage\Entity\Reference;

/**
 * Class MonitorReference
 * @package App\Module\Monitor\Entity
 */
class MonitorReference extends Reference {

    /**
     * @var
     */
    protected $parentId = '';

    /**
     * @return mixed
     */
    public function getParentId(): string {
        return $this->parentId;
    }

    /**
     * @param string $parentId
     * @return MonitorReference
     */
    public function setParentId(string $parentId): MonitorReference {
        $this->parentId = $parentId;
        return $this;
    }
}