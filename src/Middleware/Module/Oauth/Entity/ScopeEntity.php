<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as AppEntityTrait;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ScopeEntity implements ScopeEntityInterface, EntityInterface
{
    use EntityTrait, AppEntityTrait;

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}