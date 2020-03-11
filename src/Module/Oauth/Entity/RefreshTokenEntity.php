<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as AppEntityTrait;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

/**
 * Class RefreshTokenEntity
 * @package App\Module\Oauth\Entity
 */
class RefreshTokenEntity implements RefreshTokenEntityInterface, EntityInterface
{
    use RefreshTokenTrait, EntityTrait, AppEntityTrait;
}