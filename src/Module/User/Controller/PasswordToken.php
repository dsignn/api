<?php
declare(strict_types=1);

namespace App\Module\User\Controller;

use App\Controller\RpcControllerInterface;
use App\Crypto\CryptoInterface;
use App\Mail\MailerInterface;
use App\Middleware\ContentNegotiation\ContentTypeAwareTrait;
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

    use ContentTypeAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestUserEntityHydrator';

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
     * @var MailerInterface
     */
    protected $container;

    /**
     * @var
     */
    protected $url;

    /**
     * @inheritDoc
     */
    public function __construct(UserStorageInterface $storage, CryptoInterface $crypto, RecoverPasswordMailerInterface $mailer, ContainerInterface $container) {

        $this->storage = $storage;
        $this->crypto = $crypto;
        $this->mailer = $mailer;
        $this->container = $container;

        if ($container->has('settings')) {
            $mailSetting = $container->get('settings')['mail'];
            $this->url = $mailSetting['url'];
        }
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

        $url = $this->url . '?token=' . $user->getRecoverPassword()->getToken();
        $this->mailer->send([$user->getEmail()], $this->getBodyMessage($user, $url));

        $contentTypeService = $this->getContentTypeService($request);
        return $contentTypeService->transformContentType($response, $user);
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