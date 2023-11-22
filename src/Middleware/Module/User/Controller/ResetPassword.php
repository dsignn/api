<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\AcceptTrait;
use App\Controller\RpcControllerInterface;
use App\Crypto\CryptoInterface;
use App\Mail\MailerInterface;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ResetPassword
 * @package App\Module\User\Controller
 */
class ResetPassword implements RpcControllerInterface {

    use AcceptTrait;

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

        if (!isset($data['token'])) {
            // TODO LOCALIZATION
            $response = $response->withStatus(422);

            return $this->getAcceptData($request, $response, ['errors' => 
                ['token' => "Must be not empty"]
            ]);
        }

        if (!isset($data['password'])) {
            // TODO LOCALIZATION
            $response = $response->withStatus(422);

            return $this->getAcceptData($request, $response, ['errors' => 
                ['password' => "Must be not empty"]
            ]);
        }

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

        return $this->getAcceptData($request, $response, $user);
    }
}