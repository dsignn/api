<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy;

use App\Storage\Entity\EntityPrototypeAwareTrait;
use App\Storage\Entity\EntityPrototypeInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Class HydratorStrategy
 * @package App\Hydrator\Strategy
 */
class HydratorStrategy implements StrategyInterface {

    use EntityPrototypeAwareTrait;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var bool
     */
    protected $hydrateWithPrototype;

    /**
     * HydratorStrategy constructor.
     * @param HydratorInterface $hydrator
     * @param $objectPrototype
     */
    public function __construct(HydratorInterface $hydrator, EntityPrototypeInterface $entityPrototype, bool $hydrateWithPrototype = true) {
        $this->hydrator = $hydrator;
        $this->setEntityPrototype($entityPrototype);
        $this->hydrateWithPrototype = $hydrateWithPrototype;

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

     
        
        $hydrateValue = clone $this->getEntityPrototype()->getPrototype($value);
   
        if ($value) {
            $this->hydrator->hydrate($value, $hydrateValue);
            $value = $hydrateValue;
        } else if($hydrateValue && $this->hydrateWithPrototype) {
            $value =  $hydrateValue;
        }

        return $value;
    }
}