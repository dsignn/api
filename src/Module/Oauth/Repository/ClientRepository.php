<?php
declare(strict_types=1);

namespace App\Module\Oauth\Repository;

use App\Crypto\CryptoInterface;
use App\Module\Oauth\Entity\ClientEntity;
use App\Storage\StorageInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

/**
 * Class ClientRepository
 * @package App\Module\Oauth\Repository
 */
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * @var ClientEntity
     */
    protected $client;

    /**
     * ClientRepository constructor.
     * @param StorageInterface $storage
     * @param CryptoInterface $crypto
     */
    public function __construct(StorageInterface $storage, CryptoInterface $crypto) {
        $this->storage = $storage;
        $this->crypto = $crypto;
        $this->client = new ClientEntity();
    }

    /**
     * @inheritDoc
     */
    public function getClientEntity($clientIdentifier) {
        
        if (!$this->client->getIdentifier()) {
            $resultSet = $this->storage->getAll(['identifier' => $clientIdentifier]);
            if ($resultSet->count() === 1) {
                $this->client = $resultSet->current();
            }
        }

        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType) {

        $resultSet = $this->storage->getAll(
            ['identifier' => $clientIdentifier]
        );

        $isValid = false;
        if ($resultSet->count() === 1) {
            if ($this->crypto->deCrypto($resultSet->current()->getPassword()) === $clientSecret) {
                $isValid = true;
                $this->client->setName($resultSet->current()->getName());
                $this->client->setIdentifier($resultSet->current()->getPassword());
            }
        }

        return $isValid;
    }

}