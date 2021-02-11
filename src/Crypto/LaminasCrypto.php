<?php
declare(strict_types=1);

namespace App\Crypto;

use Laminas\Crypt\BlockCipher;
use Laminas\Crypt\Symmetric\Openssl;

/**
 * Class LaminasCrypto
 * @package App\Crypto
 */
class LaminasCrypto implements CryptoInterface {

    /**
     * @var BlockCipher
     */
    protected $adapter;

    /**
     * LaminasCrypto constructor.
     * @param string|null $key
     */
    public function __construct(string $key = null) {

        $this->adapter = new BlockCipher(new Openssl(['algo' => 'aes']));
        $this->adapter ->setKey($key);
    }

    /**
     * @inheritDoc
     */
    public function crypto($data) {
        return $this->adapter->encrypt($data);
    }

    /**
     * @inheritDoc
     */
    public function deCrypto($data) {
        return $this->adapter->decrypt($data);
    }
}