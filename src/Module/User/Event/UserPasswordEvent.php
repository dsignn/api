<?php
declare(strict_types=1);

namespace App\Module\User\Event;

use App\Crypto\CryptoInterface;
use Laminas\EventManager\EventInterface;

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

        $event->getTarget()->setPassword($this->crypto->crypto(
            $event->getTarget()->getPassword()
        ));
    }
}