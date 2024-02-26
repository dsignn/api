<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Class DefaultStrategy
 * @package App\Hydrator\Strategy\Mongo
 */
class DefaultStrategy implements StrategyInterface {

    protected $default;

    /**
     * MongoDateStrategy constructor.
     * @param null $datePrototype
     * @throws \Exception
     */
    public function __construct(string $default) {

        $this->default = $default;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null) {


        if (!$value ) {
            $value = $this->default;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data) {

        if (!$value ) {
            $value = $this->default;
        }

        return $value;
    }
}