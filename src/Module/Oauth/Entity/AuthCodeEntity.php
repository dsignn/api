<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * Class AuthCodeEntity
 * @package App\Module\Oauth\Entity
 */
class AuthCodeEntity implements AuthCodeEntityInterface {
    use AuthCodeTrait, EntityTrait, TokenEntityTrait;
}