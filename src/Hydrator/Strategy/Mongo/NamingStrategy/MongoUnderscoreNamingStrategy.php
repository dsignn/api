<?php
declare(strict_types=1);

namespace App\Hydrator\Strategy\Mongo\NamingStrategy;

use Zend\Hydrator\NamingStrategy\NamingStrategyInterface;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy as ZendUnderscoreNamingStrategy;

/**
 * Class MongoUnderscoreNamingStrategy
 * @package App\Hydrator\Strategy\Mongo\NamingStrategy
 */
class MongoUnderscoreNamingStrategy implements NamingStrategyInterface {

    /**
     * @var ZendUnderscoreNamingStrategy
     */
    protected $strategy;

    public function __construct() {

        $this->strategy = new ZendUnderscoreNamingStrategy();
    }

    /**
     * @inheritDoc
     */
    public function extract(string $name, ?object $object = null): string {

        $extract = $this->strategy->extract($name, $object);
        return $extract === 'id' ? '_id' : $extract;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $name, ?array $data = null): string
    {
        return $this->strategy->hydrate($name, $data);
    }
}