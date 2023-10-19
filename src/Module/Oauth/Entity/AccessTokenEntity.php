<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;
use DateTimeImmutable;
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

    /**
     * @var DateTimeImmutable
     */
    protected $startDateTime = null;

        /**
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT()
    {
        $this->initJwtConfiguration();
//var_dump($this->getStartDateTime());
        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt($this->getStartDateTime() ? $this->getStartDateTime() : new DateTimeImmutable())
            ->canOnlyBeUsedAfter($this->getStartDateTime() ? $this->getStartDateTime() : new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    /**
     * Get the value of startDateTime
     *
     * @return  DateTimeImmutable
     */ 
    public function getStartDateTime() {
        return $this->startDateTime;
    }

    /**
     * Set the value of startDateTime
     *
     * @param  DateTimeImmutable  $startDateTime
     *
     * @return  self
     */ 
    public function setStartDateTime(DateTimeImmutable $startDateTime = null) {
        $this->startDateTime = $startDateTime;
        return $this;
    }
}