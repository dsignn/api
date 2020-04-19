<?php
declare(strict_types=1);

namespace App\Module\Timeslot\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;

/**
 * Class TimeslotEntity
 * @package App\Module\Timeslot\Entity
 */
class TimeslotEntity implements EntityInterface {

    use StorageEntityTrait;

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
     * @return TimeslotEntity
     */
    public function setName(string $name): TimeslotEntity {
        $this->name = $name;
        return $this;
    }


}