<?php
declare(strict_types=1);

namespace App\Module\Playlist\Entity;

use App\Module\Organization\Entity\OrganizationAwareInterface;
use App\Module\Organization\Entity\OrganizationAwareTrait;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class MonitorEntity
 * @package App\Module\Monitor\Entity
 */
class PlaylistEntity implements EntityInterface {

    use StorageEntityTrait;

    /**
     * @var
     */
    protected $name;

   
    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $name
     * @return PlaylistEntity
     */
    public function setName($name): PlaylistEntity {
        $this->name = $name;
        return $this;
    }
}

