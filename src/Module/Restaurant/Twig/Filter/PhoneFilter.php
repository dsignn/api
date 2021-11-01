<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Twig\Filter;

use Twig\TwigFilter;

/**
 * class PhoneFilter
 */
final class PhoneFilter {

    public static function getFilter() {
        return new TwigFilter(
            'phone',
            function($value) {

                /** @var Phone $value */
                return $value->getNumber() ?   $value->getPrefix() . ' ' . $value->getNumber() : '';
            }
        );
    }
}