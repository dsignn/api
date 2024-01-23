<?php
declare(strict_types=1);

namespace App\Crypto;

use RuntimeException;

/**
 * Class DefuseCrypto
 * @package App\Cryptp
 */
class QueryStringCrypto implements CryptoInterface {

    /**
     * @inheritDoc
     */
    public function crypto($data) {
        return bin2hex(random_bytes($data));
    }

    /**
     * @inheritDoc
     */
    public function deCrypto($data) {
        throw new RuntimeException('This crypto not decript data');
    }

}