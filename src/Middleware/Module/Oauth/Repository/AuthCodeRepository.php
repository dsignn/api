<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Module\Oauth\Entity\AuthCodeEntity;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

/**
 * Class AuthCodeRepository
 * @package App\Module\Oauth\Repository
 */
class AuthCodeRepository implements AuthCodeRepositoryInterface {

    /**
     * AuthCodeRepository constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {

        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    /**
     * @inheritDoc
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity) {
        $this->storage->save($authCodeEntity);
    }

    public function revokeAuthCode($codeId) {
        // TODO: Implement revokeAuthCode() method.
        var_dump('revokeAuthCode');
        var_dump($codeId);
        die();
    }

    public function isAuthCodeRevoked($codeId) {
        return false; // The auth code has not been revoked
    }
}