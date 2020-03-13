<?php
declare(strict_types=1);

namespace App\Storage\Adapter\Mongo\ResultSet;

use App\Storage\ObjectPrototypeInterface;
use App\Storage\ObjectPrototypeTrait;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorAwareTrait;

/**
 * Class MongoHydrateResultSet
 * @package App\Storage\Mongo
 */
class MongoHydrateResultSet extends MongoResultSet implements HydratorAwareInterface, ObjectPrototypeInterface {

   use HydratorAwareTrait, ObjectPrototypeTrait;

    /**
     * @inheritDoc
     */
    public function current()
    {
        $current = parent::current();
        if ($this->getHydrator() && $current) {
            $prototype = clone $this->getObjectPrototype();
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
            $prototype = clone $this->getObjectPrototype();
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

        if ($this->getHydrator() && count($data) > 0) {
            $hydrateArray = [];
            foreach ($data as $item) {
                $prototype = clone $this->getObjectPrototype();
                $this->getHydrator()->hydrate($item, $prototype);
                array_push($hydrateArray,  $this->getHydrator()->extract($prototype));
            }
            $data = $hydrateArray;
        }
        return $data;
    }
}