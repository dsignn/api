<?php
declare(strict_types=1);

namespace App\Storage\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;

/**
 * Class GenericEntity
 * @package App\Module\Restaurant\Entity
 */
class GenericEntity implements EntityInterface {

    use EntityTrait;
}