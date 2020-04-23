<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy;

use App\Storage\Entity\EntityPrototypeAwareInterface;
use App\Storage\Entity\EntityPrototypeAwareTrait;
use App\Storage\Entity\EntityPrototypeInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Class HydratorArrayStrategy
 * @package App\Hydrator\Strategy
 */
class HydratorArrayStrategy implements StrategyInterface, EntityPrototypeAwareInterface {

    use EntityPrototypeAwareTrait;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * HydratorArrayStrategy constructor.
     * @param HydratorInterface $hydrator
     * @param EntityPrototypeInterface $entityPrototype
     */
    public function __construct(HydratorInterface $hydrator, EntityPrototypeInterface $entityPrototype) {
        $this->hydrator = $hydrator;
        $this->setEntityPrototype($entityPrototype);
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
                $value[$cont] = $this->hydrator->hydrate($value[$cont], clone $this->getEntityPrototype()->getPrototype($value[$cont]));
            }
        }
        return $value;
    }
}