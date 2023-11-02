<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy;

use DateTimeInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use MongoDB\BSON\UTCDateTime;

/**
 * Class DateStrategy
 * @package App\Hydrator\Strategy\Mongo
 */
class DateStrategy implements StrategyInterface {

    protected $datePrototype;

    protected $format = 'Y-m-d H:i:s';

    /**
     * MongoDateStrategy constructor.
     * @param null $datePrototype
     * @throws \Exception
     */
    public function __construct($datePrototype = null, $format = null) {

        $datePrototype = $datePrototype ? $datePrototype : new \DateTime();
        $this->setDatePrototype($datePrototype);

        if ($format) {
            $this->format = $format;
        }
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {


        if ($value instanceof DateTimeInterface) {
            $value = $value->format($this->format);
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {

        $dateTime = clone $this->getDatePrototype();
        switch (true) {
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
    public function setDatePrototype(DateTimeInterface $datePrototype): DateStrategy {
        $this->datePrototype = $datePrototype;
        return $this;
    }
}