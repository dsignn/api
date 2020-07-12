<?php
declare(strict_types=1);

namespace App\Module\User\Entity;

use App\Module\User\Entity\Embedded\ActivationCode;
use App\Module\User\Entity\Embedded\RecoverPassword;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait as StorageEntityTrait;
use Laminas\Permissions\Acl\Role\RoleInterface;
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
    static public $STATUS_NOT_VERIFY = 'not-verify';

    /**
     * @var string
     */
    static public $STATUS_ENABLE = 'enable';

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $roleId = '';

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var array
     */
    protected $organizations = [];

    /**
     * @var RecoverPassword
     */
    protected $recoverPassword;

    /**
     * @var ActivationCode
     */
    protected $activationCode;

    /**
     * UserEntity constructor.
     */
    public function __construct() {
        $this->status = UserEntity::$STATUS_NOT_VERIFY;
        $this->recoverPassword = new RecoverPassword();
        $this->activationCode = new ActivationCode();
    }

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
    public function getRoleId(): string {
        return $this->roleId;
    }

    /**
     * @param string $roleId
     * @return UserEntity
     */
    public function setRoleId(string $roleId): UserEntity {
        $this->roleId = $roleId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @param string $status
     * @return UserEntity
     */
    public function setStatus(string $status): UserEntity {
        $this->status = $status;
        return $this;
    }

    /**
     * @return RecoverPassword
     */
    public function getRecoverPassword(): RecoverPassword {
        return $this->recoverPassword;
    }

    /**
     * @param RecoverPassword $recoverPassword
     * @return UserEntity
     */
    public function setRecoverPassword(RecoverPassword $recoverPassword): UserEntity {
        $this->recoverPassword = $recoverPassword;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrganizations(): array {
        return $this->organizations;
    }

    /**
     * @param array $organizations
     * @return UserEntity
     */
    public function setOrganizations(array $organizations): UserEntity {
        $this->organizations = $organizations;
        return $this;
    }

    /**
     * @return ActivationCode
     */
    public function getActivationCode(): ActivationCode {
        return $this->activationCode;
    }

    /**
     * @param ActivationCode $activationCode
     * @return UserEntity
     */
    public function setActivationCode(ActivationCode $activationCode): UserEntity {
        $this->activationCode = $activationCode;
        return $this;
    }
}