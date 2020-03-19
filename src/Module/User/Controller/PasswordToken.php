<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\RpcControllerInterface;
use App\Crypto\CryptoInterface;
use App\Mail\MailerInterface;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Mail\RecoverPasswordMailerInterface;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PasswordToken
 * @package App\Module\User\Controller
 */
class PasswordToken implements RpcControllerInterface {

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ContainerInterface
     */
    protected $crypto;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @inheritDoc
     */
    public function __construct(UserStorageInterface $storage, CryptoInterface $crypto, RecoverPasswordMailerInterface $mailer) {

        $this->storage = $storage;
        $this->crypto = $crypto;
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $data = $request->getParsedBody();

        // TODO validate data

        $resultSet = $this->storage->getAll(['email' => $data['identifier']]);
        /** @var UserEntity $user */
        $user = $resultSet->current();
        if (!$user) {
            return $response->withStatus(404);
        }

        $user->getRecoverPassword()->setDate(new \DateTime())
            ->setToken($this->crypto->crypto($user->getRecoverPassword()->getDate()->format('Y-m-d H:i:s')));

        $this->storage->update($user);

        // TODO send mail
        $this->mailer->send(['test'], 'test');
        die();
    }
}