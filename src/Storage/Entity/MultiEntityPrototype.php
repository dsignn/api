<?php
declare(strict_types=1);

namespace App\Storage\Entity;

use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Class SingleEntityPrototype
 * @package App\Storage\Entity
 */
class MultiEntityPrototype implements EntityPrototypeInterface {

    /**
     * @var array
     */
    protected $arrayPrototype = [];

    /**
     * @var string
     */
    protected $propertyFiled = '';

    /**
     * @var NamingStrategyInterface
     */
    protected $nameStrategy;

    /**
     * MultiEntityPrototype constructor.
     * @param $propertyFiled
     */
    public function __construct($propertyFiled) {
        $this->propertyFiled = $propertyFiled;
        $this->nameStrategy = new UnderscoreNamingStrategy();
    }

    /**
     * @param string $type
     * @param $entityPrototype
     * @return MultiEntityPrototype
     * @throws \Exception
     */
    public function addEntityPrototype(string $type, $entityPrototype): MultiEntityPrototype {
        if (!is_object($entityPrototype)) {
            throw new \Exception('entity prototype must be and object');
        }

        $this->arrayPrototype[$type] = $entityPrototype;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPrototype($data = null) {

        $entity = null;
        switch (true) {
            case is_object($data) === true && method_exists($data, 'get' . ucfirst($this->propertyFiled)) === true:
                $entity = isset($this->arrayPrototype[$data->{'get' . ucfirst($this->propertyFiled)}()]) ? clone $this->arrayPrototype[$data->{'get' . ucfirst($this->propertyFiled)}()] : null;
                break;
            case is_object($data) === true && property_exists($data, $this->propertyFiled) === true:
                $entity = isset($this->arrayPrototype[$data->{$this->propertyFiled}]) ? clone $this->arrayPrototype[$data->{$this->propertyFiled}] : null;
                break;
            case is_array($data) === true && isset($data[$this->propertyFiled]) && isset($this->arrayPrototype[$data[$this->propertyFiled]]) === true:
                $entity = isset($this->arrayPrototype[$data[$this->propertyFiled]]) ? clone $this->arrayPrototype[$data[$this->propertyFiled]] : null;
                break;
            case is_array($data) === true && isset($data[$this->nameStrategy->extract($this->propertyFiled)]) && isset($this->arrayPrototype[$data[$this->nameStrategy->extract($this->propertyFiled)]]) === true:
                $entity = isset($this->arrayPrototype[$data[$this->nameStrategy->extract($this->propertyFiled)]]) ? clone $this->arrayPrototype[$data[$this->nameStrategy->extract($this->propertyFiled)]] : null;
                break;
        }
        return $entity;
    }

    /**
     * @return NamingStrategyInterface
     */
    public function getNameStrategy() {
        return $this->nameStrategy;
    }

    /**
     * @param NamingStrategyInterface $nameStrategy
     * @return MultiEntityPrototype
     */
    public function setNameStrategy(NamingStrategyInterface $nameStrategy): MultiEntityPrototype {
        $this->nameStrategy = $nameStrategy;
        return $this;
    }
}