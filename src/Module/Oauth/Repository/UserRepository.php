<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Crypto\CryptoInterface;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

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

        if ($resultSet->count() === 1) {

            $userEntity = $resultSet->current();
            if ($this->crypto->deCrypto($userEntity->getPassword()) === $password) {
                $user = $userEntity;
            }
        }
        return $user;
    }
}