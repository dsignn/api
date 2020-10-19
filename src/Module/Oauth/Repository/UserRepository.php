<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Crypto\CryptoInterface;
use App\Module\Oauth\Exception\UsernameConflictException;
use App\Module\Oauth\Exception\UsernameNotFoundException;
use App\Module\Oauth\Exception\UserNotEnableException;
use App\Module\User\Entity\UserEntity;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Slim\Exception\HttpException;
use Slim\Psr7\Request;

/**
 * Class UserRepository
 * @package App\Module\Oauth\Repository
 */
class UserRepository implements UserRepositoryInterface {

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * UserRepository constructor.
     * @param StorageInterface $storage
     * @param CryptoInterface $crypto
     */
    public function __construct(StorageInterface $storage, CryptoInterface $crypto) {

        $this->storage = $storage;
        $this->crypto = $crypto;
    }

    /**
     * @inheritDoc
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity) {

        $resultSet = $this->storage->getAll(['email' => $username]);
        $user = null;
        switch (true) {
            case $resultSet->count() === 1:
                $userEntity = $resultSet->current();
                if ($this->crypto->deCrypto($userEntity->getPassword()) === $password) {
                    /** @var UserEntity $user */
                    $user = $userEntity;
                    switch (true) {
                        case $user->getStatus() === UserEntity::$STATUS_NOT_VERIFY:
                            throw new UserNotEnableException(
                                "",
                                401);
                            break;
                    }
                }
                break;
            case $resultSet->count() === 0:
                throw new UsernameNotFoundException();
                break;
            default:
                throw new UsernameConflictException();
        }
        var_dump('ttttt');
        die();
        return $user;
    }
}