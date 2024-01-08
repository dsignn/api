<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy\Mongo;

use Laminas\Hydrator\Strategy\StrategyInterface;
use MongoDB\BSON\ObjectId;

/**
 * Class MongoIdStrategy
 * @package App\Hydrator\Strategy
 */
class MongoIdStrategy implements StrategyInterface {

    /**
     * @var bool
     */
    protected $autoGenerate = false;

    /**
     * MongoIdStrategy constructor.
     * @param bool $autoGenerate
     */
    public function __construct(bool $autoGenerate) {
        $this->autoGenerate = $autoGenerate;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {

        switch (true) {
            case !!$value && is_string($value) === true:
                $value = new ObjectId($value);
                break;
            case !$value === true && $this->autoGenerate === true:
                $value = new ObjectId();
                break;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {

        switch (true) {
            case $value instanceof ObjectId === true:
                $value = $value->__toString();
                break;
            case is_array($value) === true:
            case is_object($value) === true:
                $value = '';
                break;
        }

        return $value;
    }
}