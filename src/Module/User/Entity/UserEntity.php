<?php
declare(strict_types=1);

namespace App\Module\User\Entity;

use App\Auth\RoleInterface;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class UserEntity
 * @package App\Module\User\Entity
 */
class UserEntity implements EntityInterface, UserEntityInterface, RoleInterface {

    use StorageEntityTrait;

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @var string
     */
    protected $role = '';

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail(string $email): UserEntity {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword(string $password): UserEntity {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentifier() {
        return $this->email;
    }

    /**
     * @param $identifier
     * @return UserEntity
     */
    public function setIdentifier($identifier): UserEntity {
        $this->email = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserEntity
     */
    public function setName(string $name): UserEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return UserEntity
     */
    public function setLastName(string $lastName): UserEntity {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void {
        $this->role = $role;
    }
}