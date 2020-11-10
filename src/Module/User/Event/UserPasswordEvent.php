<?php
declare(strict_types=1);

namespace App\Module\User\Event;

use App\Crypto\CryptoInterface;
use App\Module\User\Entity\UserEntity;
use Laminas\Crypt\BlockCipher;
use Laminas\Crypt\Symmetric\Openssl;
use Laminas\EventManager\EventInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class UserPasswordEvent
 * @package App\Module\User\Event
 */
class UserPasswordEvent {

    /**
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * UserPasswordEvent constructor.
     * @param CryptoInterface $crypto
     */
    public function __construct(CryptoInterface $crypto) {
        $this->crypto = $crypto;
    }

    /**
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event) {

        /** @var UserEntity $user */
        $data = $event->getTarget()->getData();
        if ($data['password']) {
            $data['password'] = $this->crypto->crypto($data['password']);
        }

        $event->getTarget()->setData($data);
    }
}