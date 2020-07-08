<?php

declare(strict_types=1);

namespace App\Hydrator\Strategy\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\UnderscoreToCamelCaseFilter;

/**
 * Class CamelCaseStrategy
 * @package App\Hydrator\NameStrategy
 */
class CamelCaseStrategy implements NamingStrategyInterface {

    /**
     * @var UnderscoreToCamelCaseFilter|null
     */
    private static $underscoreToCamelCaseFilter;

    /**
     * @param string $name
     * @param array|null $data
     * @return string
     */
    public function hydrate(string $name, ?array $data = null): string
    {
        return $this->getUnderscoreToCamelCaseFilter()->filter($name);
    }

    /**
     * @param string $name
     * @param object|null $object
     * @return string
     */
    public function extract(string $name, ?object $object = null): string
    {
        return $this->getUnderscoreToCamelCaseFilter()->filter($name);
    }

    /**
     * @return UnderscoreToCamelCaseFilter
     */
    private function getUnderscoreToCamelCaseFilter() : UnderscoreToCamelCaseFilter
    {
        if (! static::$underscoreToCamelCaseFilter) {
            static::$underscoreToCamelCaseFilter = new UnderscoreToCamelCaseFilter();
        }

        return static::$underscoreToCamelCaseFilter;
    }

}