<?php
declare(strict_types=1);

namespace App\Crypto;

/**
 * Interface CryptoInterface
 * @package App\Crypto
 */
interface CryptoInterface
{
    /**
     * @param $data
     * @return string
     */
    public function crypto($data);

    /**
     * @param $data
     * @return string
     */
    public function deCrypto($data);
}