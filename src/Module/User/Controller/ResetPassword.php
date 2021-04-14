<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\RpcControllerInterface;
use App\Crypto\CryptoInterface;
use App\Mail\MailerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PasswordToken
 * @package App\Module\User\Controller
 */
class ResetPassword implements RpcControllerInterface {

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
     * @var MailerInterface
     */
    protected $container;

    /**
     * @var ContainerInterface
     */
    protected $crypto;

    /**
     * @inheritDoc
     */
    public function __construct(UserStorageInterface $storage, CryptoInterface $crypto, ContainerInterface $container) {

        $this->storage = $storage;
        $this->crypto = $crypto;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $data = $request->getParsedBody();

        // TODO validation

        $resultSet = $this->storage->getAll(['recover_password.token' => $data['token']]);
        /** @var UserEntity $user */
        $user = $resultSet->current();
        if (!$user) {
            return $response->withStatus(404);
        }

        // TODO validation date expirate

        $user->setPassword($this->crypto->crypto($data['password']));
        $user->getRecoverPassword()->setToken('')
            ->setDate(null);

        if ($user->getStatus() === UserEntity::$STATUS_NOT_VERIFY) {
            $user->setStatus(UserEntity::$STATUS_ENABLE);
        }

        $this->storage->update($user);

        $AcceptService = $this->getAcceptService($request);
        return $AcceptService->transformAccept($response, $user);
    }
}