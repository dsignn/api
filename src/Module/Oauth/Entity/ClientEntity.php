<?php
declare(strict_types=1);

namespace App\Module\Oauth\Entity;

use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * Class ClientEntity
 * @package App\Module\Oauth\Entity
 */
class ClientEntity implements ClientEntityInterface, EntityInterface
{
    use ClientTrait, EntityTrait, StorageEntityTrait;

    /**
     * @var
     */
    protected $password;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     * @return ClientEntity
     */
    public function setPassword($password): ClientEntity
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param $redirectUri
     * @return ClientEntity
     */
    public function setRedirectUri($redirectUri): ClientEntity {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    /**
     * @param bool $isConfidential
     * @return ClientEntity
     */
    public function setIsConfidential(bool $isConfidential): ClientEntity {
        $this->isConfidential = $isConfidential;
        return $this;
    }


}