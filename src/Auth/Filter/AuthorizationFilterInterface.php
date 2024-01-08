<?php
declare(strict_types=1);

namespace App\Auth\Filter;

/**
 * Interface AuthorizationFilterInterface
 * @package App\Auth\Filter
 */
interface AuthorizationFilterInterface {

    /**
     * @param string $name
     * @return AuthorizationFilterInterface
     */
    public function removeFilter(string $name): AuthorizationFilterInterface;

    /**
     * @param string $name
     * @param $value
     * @return AuthorizationFilterInterface
     */
    public function addFilter(string $name, $value): AuthorizationFilterInterface;

    /**
     * @return array
     */
    public function getFilters(): array;
}