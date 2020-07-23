<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
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

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestUserEntityHydrator';

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

        $data = $request->getParsedBody();

        // TODO validation

        $resultSet = $this->storage->getAll(['activation_code.token' => $data['token']]);
        /** @var UserEntity $user */
        $user = $resultSet->current();
        if (!$user) {
            return $response->withStatus(404);
        }

        $user->setStatus(UserEntity::$STATUS_ENABLE);
        $this->storage->update($user);

        $AcceptService = $this->getAcceptService($request);
        return $AcceptService->transformAccept($response, $user);
    }
}