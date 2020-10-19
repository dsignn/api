<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\RpcControllerInterface;
use App\Crypto\CryptoInterface;
use App\Mail\Contact;
use App\Mail\ContactInterface;
use App\Mail\MailerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Mail\UserMailerInterface;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\StorageInterface;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PasswordToken
 * @package App\Module\User\Controller
 */
class PasswordToken implements RpcControllerInterface {

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
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * @var UserMailerInterface
     */
    protected $mailer;

    /**
     * @var ContactInterface
     */
    protected $from;

    /**
     * @var
     */
    protected $url;

    /**
     * @inheritDoc
     */
    public function __construct(UserStorageInterface $storage, CryptoInterface $crypto, UserMailerInterface $mailer, ContainerInterface $container) {

        $this->storage = $storage;
        $this->crypto = $crypto;
        $this->mailer = $mailer;
        // TODO best way to inject service...
        $this->from = $container->get('UserFrom');
        $this->url = $container->get('settings')['mail']['resetPassword'];
        $this->container = $container;
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

        $url = $this->url . '?token=' . urlencode($user->getRecoverPassword()->getToken());
        $toContact = new Contact();
        $toContact->setEmail($user->getEmail());
        $toContact->setName($user->getName());
        $this->mailer->send([$toContact], $this->from ,'Change password' ,$this->getBodyMessage($user, $url));

        $AcceptService = $this->getAcceptService($request);
        return $AcceptService->transformAccept($response, $user);
    }

    /**
     * @param UserEntity $user
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function getBodyMessage(UserEntity $user, $url) {

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Mail/Template/');
        $twig = new \Twig\Environment($loader, []);

        return $twig->render('reset-password.html', ['user' => $user, 'url' => $url]);
    }
}