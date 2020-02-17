<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy\Mongo;

use DateTimeImmutable;
use DateTimeInterface;
use MongoDate;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class MongoDateStrategy
 * @package App\Hydrator\Strategy\Mongo
 */
class MongoDateStrategy implements StrategyInterface {

    protected $datePrototype;

    /**
     * MongoDateStrategy constructor.
     * @param null $datePrototype
     * @throws \Exception
     */
    public function __construct($datePrototype = null) {

        $datePrototype = $datePrototype ? $datePrototype : new DateTimeImmutable();
        $this->setDatePrototype($datePrototype);
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {

        if ($value instanceof DateTimeInterface) {
            $value = new MongoDate($value->getTimestamp());
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {

        if ($value instanceof MongoDate) {
            $dateTime = clone $this->getDatePrototype();
            $dateTime->setTimestamp($value->sec);
            $value = $dateTime;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getDatePrototype() {
        return $this->datePrototype;
    }

    /**
     * @inheritDoc
     */
    public function setDatePrototype(DateTimeInterface $datePrototype): MongoDateStrategy {
        $this->datePrototype = $datePrototype;
        return $this;
    }
}