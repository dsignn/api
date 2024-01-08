<?php
declare(strict_types=1);

namespace App\Module\Oauth\Filter;

use App\Crypto\CryptoInterface;
use Laminas\Filter\FilterInterface;

/**
 * Class PasswordFilter
 * @package App\Module\Oauth\Filter
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
     * @inheritDoc
     */
    public function filter($value) {
        return $this->crypto->crypto($value);
    }
}