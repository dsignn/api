<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy\Mongo;

use DateTimeInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use MongoDate;

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
            switch (true) {
                case $dateTime instanceof \DateTimeImmutable === true:
                    /** @var \DateTimeImmutable $dateTime */
                    $dateTime = \DateTimeImmutable::createFromMutable((new \DateTime())->setTimestamp($value->sec));
                    break;
                default:
                    $dateTime->setTimestamp($value->sec);
            }
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