<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Twig\Filter;

use Twig\TwigFilter;

/**
 * class CurrencyFilter
 */
final class CurrencyFilter {

    public static function getFilter() {
        return new TwigFilter(
            'currency',
            function($value) {

                switch($value) {
                    case 'EUR':
                        return '€';
                }

                return $value;
            }
        );
    }
}