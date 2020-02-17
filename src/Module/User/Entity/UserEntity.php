<?php
declare(strict_types=1);

namespace App\Module\User\Entity;

use App\Storage\Entity\EntityTrait as StorageEntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class UserEntity
 * @package App\Module\User\Entity
 */
class UserEntity implements UserEntityInterface {

    use StorageEntityTrait;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail(string $email): UserEntity
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword(string $password): UserEntity
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->email;
    }

    /**
     * @param $identifier
     * @return UserEntity
     */
    public function setIdentifier($identifier): UserEntity
    {
        $this->email = $identifier;
        return $this;
    }
}