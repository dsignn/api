<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy\Mongo;

use Laminas\Hydrator\Strategy\StrategyInterface;
use MongoId;

/**
 * Class MongoIdStrategy
 * @package App\Hydrator\Strategy
 */
class MongoIdStrategy implements StrategyInterface {

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {

        if ($value && is_string($value)) {
            $value = new MongoId($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {

        if ($value instanceof MongoId) {
            $value = $value->__toString();
        }

        return $value;
    }
}