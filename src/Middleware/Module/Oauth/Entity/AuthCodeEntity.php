<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as AppEntityTrait;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * Class AuthCodeEntity
 * @package App\Module\Oauth\Entity
 */
class AuthCodeEntity implements AuthCodeEntityInterface, EntityInterface {
    use AuthCodeTrait, EntityTrait, TokenEntityTrait, AppEntityTrait;
}