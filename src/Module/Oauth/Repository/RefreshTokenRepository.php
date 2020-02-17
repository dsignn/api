<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Module\Oauth\Entity\RefreshTokenEntity;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

/**
 * Class RefreshTokenRepository
 * @package App\Module\Oauth\Repository
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface {

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * AccessTokenRepository constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {

        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function getNewRefreshToken() {
        return new RefreshTokenEntity();
    }

    /**
     * @inheritDoc
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity) {
        $this->storage->save($refreshTokenEntity);
    }

    /**
     * @inheritDoc
     */
    public function revokeRefreshToken($tokenId) {
        return false; // The refresh token has not been revoked
    }

    /**
     * @inheritDoc
     */
    public function isRefreshTokenRevoked($tokenId) {

        $resultSet = $this->storage->gelAll(['identifier' => $tokenId]);

        $isValid = true;
        if ($resultSet->count() === 1) {
            // TODO check
            $isValid = false;
        }

        return $isValid;
    }
}