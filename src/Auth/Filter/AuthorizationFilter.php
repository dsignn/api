<?php
declare(strict_types=1);

namespace App\Auth\Filter;

/**
 * Class AuthorizationFilter
 * @package App\Auth\Filter
 */
class AuthorizationFilter implements AuthorizationFilterInterface {

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @inheritDoc
     */
    public function removeFilter(string $name): AuthorizationFilterInterface {
        if (isset($this->filters[$name])) {
            unset($this->filters[$name]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFilter(string $name, $value): AuthorizationFilterInterface {
        $this->filters[$name] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array {
        return $this->filters;
    }
}