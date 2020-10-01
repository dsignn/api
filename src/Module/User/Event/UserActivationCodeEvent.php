<?php
declare(strict_types=1);

namespace App\Module\User\Event;

use App\Crypto\CryptoInterface;
use App\Mail\Contact;
use App\Mail\ContactInterface;
use App\Module\User\Entity\UserEntity;
use App\Module\User\Mail\UserMailerInterface;
use Laminas\EventManager\EventInterface;

/**
 * Class UserActivationCodeEvent
 * @package App\Module\User\Event
 */
class UserActivationCodeEvent
{

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
     * @var string
     */
    protected $url;

    /**
     * UserActivationCodeEvent constructor.
     * @param CryptoInterface $crypto
     * @param UserMailerInterface $mailer
     * @param ContactInterface $from
     * @param $url
     */
    public function __construct(CryptoInterface $crypto, UserMailerInterface $mailer, ContactInterface $from, string $url) {
        $this->crypto = $crypto;
        $this->mailer = $mailer;
        $this->from = $from;
        $this->url = $url;
    }

    /**
     * @param EventInterface $event
     * @throws \Exception
     */
    public function __invoke(EventInterface $event) {
var_dump( $event->getTarget());
die();
        $event->getTarget()->getActivationCode()->setDate(new \DateTime())
            ->setToken($this->crypto->crypto($event->getTarget()->getActivationCode()->getDate()->format('Y-m-d H:i:s')));

      //  $this->sendActivationMail($event->getTarget());
    }

    /**
     * @param UserEntity $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function sendActivationMail(UserEntity $user) {

        $toContact = new Contact();
        $toContact->setEmail($user->getEmail());
        $toContact->setName($user->getName());
        $url = $this->url . '?token=' . $user->getActivationCode()->getToken();
        try {
            $this->mailer->send([$toContact], $this->from, 'Activation code', $this->getBodyMessage($user, $url));
        } catch (\Exception $exception) {
            $user->setStatus(UserEntity::$STATUS_ACTIVATION_MAIL_ERROR);
        }
    }


    /**
     * @param UserEntity $user
     * @param string $url
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function getBodyMessage(UserEntity $user, string $url) {

        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Mail/Template/');
        $twig = new \Twig\Environment($loader, []);

        return $twig->render('activation.html', ['user' => $user, 'url' => $url]);
    }
}