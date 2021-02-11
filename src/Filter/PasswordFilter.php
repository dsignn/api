<?php
declare(strict_types=1);

namespace App\Filter;

use App\Crypto\CryptoInterface;
use Laminas\Filter\FilterInterface;

/**
 * Class PasswordFilter
 * @package App\Filter
 */
class PasswordFilter implements FilterInterface {

    /**
     * @var CryptoInterface
     */
    protected $crypto;

    /**
     * PasswordFilter constructor.
     * @param CryptoInterface $crypto
     */
    public function __construct(CryptoInterface $crypto) {
        $this->crypto = $crypto;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function filter($value){

        if ($value) {
            $value = $this->crypto->crypto($value);
        }

        return $value;
    }
}