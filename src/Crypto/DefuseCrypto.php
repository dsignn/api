<?php
declare(strict_types=1);

namespace App\Crypto;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

/**
 * Class DefuseCrypto
 * @package App\Cryptp
 */
class DefuseCrypto implements CryptoInterface {

    /**
     * @var Key
     */
    protected $key;

    public function __construct(Key $key) {
        $this->key = $key;
    }

    /**
     * @inheritDoc
     */
    public function crypto($data) {
       return Crypto::encrypt($data, $this->key);
    }

    /**
     * @inheritDoc
     */
    public function deCrypto($data) {
        return Crypto::decrypt($data, $this->key);
    }
}