<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy\Mongo;

use DateTimeInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use MongoDate;
use MongoDB\BSON\UTCDateTime;

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

        $datePrototype = $datePrototype ? $datePrototype : new \DateTime();
        $this->setDatePrototype($datePrototype);
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {


        if ($value instanceof DateTimeInterface) {
            $value = new UTCDateTime($value);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {

        $dateTime = clone $this->getDatePrototype();
        switch (true) {
            /*
            case $value instanceof MongoDate === true && $dateTime instanceof \DateTimeImmutable === true:
                /** @var MongoDate $value
                $value = \DateTimeImmutable::createFromMutable((new \DateTime())->setTimestamp($value->sec));
                break;
            case $value instanceof MongoDate === true && $dateTime instanceof \DateTime === true:
                /** @var MongoDate $value
                $value = $dateTime->setTimestamp($value->sec);
                break;
            */
            case $value instanceof UTCDateTime === true && $dateTime instanceof \DateTimeImmutable === true:
                /** @var UTCDateTime $value */
                $value = \DateTimeImmutable::createFromMutable($value->toDateTime());
                break;
            case $value instanceof UTCDateTime === true && $dateTime instanceof \DateTime === true:
                /** @var UTCDateTime $value */
                $value = $value->toDateTime();
                break;
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