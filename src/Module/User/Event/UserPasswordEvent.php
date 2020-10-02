<?php
declare(strict_types=1);

namespace App\Module\User\Event;

use App\Crypto\CryptoInterface;
use Laminas\Crypt\BlockCipher;
use Laminas\Crypt\Symmetric\Openssl;
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

        var_dump($event->getTarget()->getPassword());


        $blockCipher = new BlockCipher(new Openssl(['algo' => 'aes']));
        $blockCipher->setKey('encryption key');
        $u = $blockCipher->encrypt('this is a secret message');
        var_dump($u);
        $k = $blockCipher->decrypt($u);
        var_dump($k);
        /*
        var_dump($this->crypto->crypto(
         'frocio'
        ));
        */
        die();
        $event->getTarget()->setPassword($this->crypto->crypto(
            $event->getTarget()->getPassword()
        ));
    }
}