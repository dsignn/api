<?php
declare(strict_types=1);

namespace App\Hydrator;

use App\Storage\Entity\EntityPrototypeAwareInterface;
use App\Storage\Entity\EntityPrototypeAwareTrait;
use App\Storage\Entity\MultiEntityPrototype;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Class MapHydrator
 * @package App\Hydrator
 */
class MapHydrator implements HydratorInterface, EntityPrototypeAwareInterface {

    use EntityPrototypeAwareTrait;

    /**
     * @var array
     */
    protected $hydrators =  [];

    /**
     * @var string
     */
    protected $typeField = '';

    /**
     * @var NamingStrategyInterface
     */
    protected $nameStrategy;

    /**
     * MapHydrator constructor.
     */
    public function __construct() {
        $this->nameStrategy = new UnderscoreNamingStrategy();
    }

    /**
     * @param string $type
     * @param HydratorInterface $hydrator
     * @return $this
     */
    public function addHydrator(string $type, HydratorInterface $hydrator): MapHydrator {
        $this->hydrators[$type] = $hydrator;
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function removeHydrator(string $type): MapHydrator {
        if (isset($this->hydrators[$type])) {
            unset($this->hydrators[$type]);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeField(): string {
        return $this->typeField;
    }

    /**
     * @param string $typeField
     * @return MapHydrator
     */
    public function setTypeField(string $typeField): MapHydrator {
        $this->typeField = $typeField;
        return  $this;
    }

    /**
     * @param object $object
     * @return array
     * @throws \Exception
     */
    public function extract(object $object): array {

        if (!$this->hasTypeField($object)) {
            throw new \Exception('Field not found in object');
        }

        $field = $this->extractTypeField($object);

        if (!isset($this->hydrators[$field])) {
            throw new \Exception(sprintf('Field %field not set', $field));
        }

        return $this->hydrators[$field]->extract($object);
    }

    /**
     * @param array $data
     * @param object $object
     * @return mixed|object
     * @throws \Exception
     */
    public function hydrate(array $data, object $object) {

        if (!isset($data[$this->typeField]) && !isset($data[$this->getNameStrategy()->extract($this->typeField)])) {
            throw new \Exception('Property not found in array');
        }

        $field = isset($data[$this->typeField]) ? $data[$this->typeField] : $data[$this->getNameStrategy()->extract($this->typeField)];

        if (!isset($this->hydrators[$field])) {
            throw new \Exception(sprintf('Field %field not set', $field));
        }

        $object = $object ? $object : $this->getEntityPrototype()->getPrototype($data);
        return $this->hydrators[$field]->hydrate($data, $object);
    }

    /**
     * @param object $object
     * @return bool
     */
    protected function hasTypeField(object $object) {
        $has = false;
        switch (true) {
            case method_exists($object, 'get' . ucfirst($this->typeField)) === true :
            case property_exists($object, $this->typeField) === true :
            case method_exists($object, 'get' . ucfirst($this->getNameStrategy()->hydrate($this->typeField))) === true :
                $has = true;
                break;
        }

        return $has;
    }

    /**
     * @param object $object
     * @return string
     */
    protected function extractTypeField(object $object) {
        $field = '';
        switch (true) {
            case method_exists($object, 'get' . ucfirst($this->typeField)) === true :
                $field = $object->{'get' . ucfirst($this->typeField)}();
                break;
            case method_exists($object, 'get' . $this->getNameStrategy()->hydrate($this->typeField)) === true :
                $field = $object->{'get' . ucfirst($this->getNameStrategy()->hydrate($this->typeField))}();
                break;
            case property_exists($object, $this->typeField) === true :
                $field = $object->{$this->typeField};
                break;
        }

        return $field;
    }

    /**
     * @return NamingStrategyInterface
     */
    public function getNameStrategy() {
        return $this->nameStrategy;
    }

    /**
     * @param NamingStrategyInterface $nameStrategy
     * @return MapHydrator
     */
    public function setNameStrategy(NamingStrategyInterface $nameStrategy): MultiEntityPrototype {
        $this->nameStrategy = $nameStrategy;
        return $this;
    }

}