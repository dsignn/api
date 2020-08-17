<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\Entity\EntityPrototypeAwareInterface;
use App\Storage\Entity\EntityPrototypeAwareTrait;
use App\Storage\ObjectPrototypeInterface;
use App\Storage\ObjectPrototypeTrait;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorAwareTrait;

/**
 * Class MongoHydrateResultSet
 * @package App\Storage\Mongo
 */
class MongoHydrateResultSet extends MongoResultSet implements HydratorAwareInterface, EntityPrototypeAwareInterface, ObjectPrototypeInterface {

   use HydratorAwareTrait, EntityPrototypeAwareTrait, ObjectPrototypeTrait;

    /**
     * @inheritDoc
     */
    public function current()
    {
        $current = parent::current();
        if ($this->getHydrator() && $current) {
            $prototype = clone $this->getEntityPrototype()->getPrototype($current);
            $this->getHydrator()->hydrate($current, $prototype);
            $current = $prototype;
        }
        return $current;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $next = parent::next();
        if ($this->getHydrator() && $next) {
            $prototype = clone $this->getEntityPrototype()->getPrototype($next);
            $this->getHydrator()->hydrate($next, $prototype);
            $next = $prototype;
        }
        return $next;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array {
        // TODO Better solution
        $data = parent::toArray();

        if ($this->getHydrator()) {
            $hydrateArray = [];
            foreach ($data as $item) {
                $prototype = clone $this->getEntityPrototype()->getPrototype($item);
                $this->getHydrator()->hydrate($item, $prototype);
                array_push($hydrateArray,  $this->getHydrator()->extract($prototype));
            }
            $data = $hydrateArray;
        }

        return $data;
    }
}