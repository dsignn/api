<?php
declare(strict_types=1);

namespace App\Module\Organization\Util;

/**
 * Class UrlNormalizer
 * @package App\Module\Organization\Util
 */
final class UrlNormalizer {

    /**
     * @param string $name
     * @return mixed
     */
    static public function normalize(string $name) {
        return str_replace(" ", '-', $name);
    }
}