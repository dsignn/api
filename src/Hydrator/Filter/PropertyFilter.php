<?php
declare(strict_types=1);

namespace App\Hydrator\Filter;

use Laminas\Hydrator\Filter\FilterInterface;

/**
 * Class PropertyFilter
 * @package App\Hydrator\Filter
 */
class PropertyFilter implements FilterInterface {

    /**
     * @var string
     */
    protected $property;

    /**
     * PropertyFilter constructor.
     * @param string $property
     */
    public function __construct(string $property) {
        $this->property = $property;
    }

    /**
     * @inheritDoc
     */
    public function filter(string $property): bool {
        return $property !== $this->property;
    }
}