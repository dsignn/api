<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\AcceptTrait;
use App\Controller\RpcControllerInterface;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ActivationToken
 * @package App\Module\User\Controller
 */
class ActivationToken implements RpcControllerInterface {

    use AcceptTrait;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * ActivationToken constructor.
     * @param UserStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(UserStorageInterface $storage, ContainerInterface $container) {
        $this->storage = $storage;
        $this->container =  $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $data = $request->getQueryParams();

        // TODO validation

        if (count($data) === 0 || !isset($data['token'])) {
            // TODO LOCALIZATION
            $response = $response->withStatus(422);

            return $this->getAcceptData($request, $response, ['errors' => 'No token in query string']);
        }

        $resultSet = $this->storage->getAll(['activation_code.token' => $data['token']]);
        /** @var UserEntity $user */
        $user = $resultSet->current();
        if (!$user) {c
            return $response->withStatus(404);
        }

        $user->setStatus(UserEntity::$STATUS_ENABLE);
        $this->storage->update($user);

        return $this->getAcceptData($request, $response, $user);
    }
}