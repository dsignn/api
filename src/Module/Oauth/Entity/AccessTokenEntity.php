<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * Class AccessTokenEntity
 * @package App\Module\Oauth\Entity
 */
class AccessTokenEntity implements AccessTokenEntityInterface, EntityInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait, StorageEntityTrait;
}