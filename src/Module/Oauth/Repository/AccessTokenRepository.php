<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Module\Oauth\Entity\AccessTokenEntity;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

/**
 * Class AccessTokenRepository
 * @package App\Module\Oauth\Repository
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface {

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

    public function getStorage() {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null) {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
        $this->storage->save($accessTokenEntity);
    }

    public function getByIdentifier(string $identifier) {
        $resultSet = $this->storage->getAll(['user_identifier' => $identifier]);
        if ($resultSet->count() > 1) {
            // TODO log error
        }

        return $resultSet->current();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId) {
        $resultSet = $this->storage->getAll(['identifier' => $tokenId]);

        if ($resultSet->count() === 1) {
            $this->storage->delete($resultSet->current());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId) {
        return false; // Access token hasn't been revoked
    }
}