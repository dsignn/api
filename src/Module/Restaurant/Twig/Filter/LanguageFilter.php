<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Twig\Filter;

use Twig\TwigFilter;

/**
 * class LanguageFilter
 */
final class LanguageFilter {

    public static function getFilter() {
        return new TwigFilter(
            'lang',
            function($value, $lang) {

                if (isset($value[$lang])) {
                    return $value[$lang];
                }
            }
        );
    }
}