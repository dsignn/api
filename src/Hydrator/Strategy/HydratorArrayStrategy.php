<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Class HydratorArrayStrategy
 * @package App\Hydrator\Strategy
 */
class HydratorArrayStrategy implements StrategyInterface {

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
        if (is_array($value) === true) {
            for ($cont = 0;  count($value) > $cont; $cont++) {
                $value[$cont] = $this->hydrator->extract($value[$cont]);
            }
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {
        if (is_array($value) === true) {
            for ($cont = 0;  count($value) > $cont; $cont++) {
                $value[$cont] = $this->hydrator->hydrate($value[$cont], clone $this->objectPrototype);
            }
        }
        return $value;
    }
}