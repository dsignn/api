<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\StorageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ActivationToken
 * @package App\Module\User\Controller
 */
class ActivationToken implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RpcPasswordUserEntityHydrator';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * ActivationToken constructor.
     * @param UserStorageInterface $storage
     */
    public function __construct(UserStorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $query = $request->getQueryParams();
        if (!isset($query['token'])) {
            return $response->withStatus(400);
        }

        $resultSet = $this->storage->getAll(['activation_code.token' => $query['token']]);
        /** @var UserEntity $user */
        $user = $resultSet->current();
        if (!$user) {
            return $response->withStatus(404);
        }

        $user->setStatus(UserEntity::$STATUS_ENABLE);
        $this->storage->update($user);
        return $response->withStatus(200);
    }
}