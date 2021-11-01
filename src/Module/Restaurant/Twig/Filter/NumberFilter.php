<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Twig\Filter;

use Twig\TwigFilter;

/**
 * class NumberFilter
 */
final class NumberFilter {

    public static function getFilter() {
        return new TwigFilter(
            'number',
            function($value, $lang) {

               
                switch($lang) {
                    case 'en':
                    case 'it':
                        return number_format($value, 2, '.', '');
                }

                return $value;
            }
        );
    }
}