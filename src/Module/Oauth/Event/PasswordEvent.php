<?php
declare(strict_types=1);

namespace App\Module\Oauth\Event;

use App\Crypto\CryptoInterface;
use App\Module\Oauth\Entity\ClientEntity;
use App\Module\Resource\Entity\AbstractResourceEntity;
use Laminas\EventManager\EventInterface;

/**
 * Class PasswordEvent
 * @package App\Module\Oauth\Event
 */
class PasswordEvent {

    /**
     * @var CryptoInterface
     */
    protected $crypto = [];

    /**
     * PasswordEvent constructor.
     * @param array $crypto
     */
    public function __construct(CryptoInterface $crypto) {
        $this->crypto = $crypto;
    }

    /**
     * @param EventInterface $event
     * @throws \Exception
     */
    public function __invoke(EventInterface $event) {

        /** @var ClientEntity $entity */
        $entity = $event->getTarget();
        $entity->setPassword($this->crypto->crypto($entity->getPassword()));
    }
}