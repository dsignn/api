<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Class HydratorStrategy
 * @package App\Hydrator\Strategy
 */
class HydratorStrategy implements StrategyInterface {

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var
     */
    protected $objectPrototype;

    /**
     * HydratorStrategy constructor.
     * @param HydratorInterface $hydrator
     * @param $objectPrototype
     */
    public function __construct(HydratorInterface $hydrator, $objectPrototype) {
        $this->hydrator = $hydrator;
        $this->objectPrototype = $objectPrototype ? $objectPrototype : new \stdClass();
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {

        if ($value) {
            $value =  $this->hydrator->extract($value);
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {
        if ($value) {

            $hydrateValue = clone $this->objectPrototype;
            $this->hydrator->hydrate($value, $hydrateValue);
            $value = $hydrateValue;
        } else if($this->objectPrototype) {
            $value =  clone $this->objectPrototype;
        }
        return $value;
    }
}