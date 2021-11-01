<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Twig\Filter;

use Twig\TwigFilter;

/**
 * class AddressFilter
 */
final class AddressFilter {

    public static function getFilter() {
        return new TwigFilter(
            'address',
            function($value) {
                /** @var Address $value */
                $address = $value->getRoute() ? $value->getRoute() : '';
                $address .= $address && $value->getStreetNumber() ?  ', ' . $value->getStreetNumber() : ($value->getStreetNumber() ? $value->getStreetNumber() : '');
                $address .= $address && $value->getCity() ?  ', ' . $value->getCity() : ($value->getCity() ? $value->getCity() : '');
                $address .= $address && $value->getPostalCode() ?  ', ' . $value->getPostalCode() : ($value->getPostalCode() ? $value->getPostalCode() : '');
                $address .= $address && $value->getState() ?  ', ' . $value->getState() : ($value->getState() ? $value->getState() : '');
                $address .= $address && $value->getCountry() ?  ', ' . $value->getCountry() : ($value->getCountry() ? $value->getCountry() : '');

                return $address;
            }
        );
    }
}