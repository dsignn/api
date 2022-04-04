<?php
declare(strict_types=1);

namespace App\Hydrator;

use Laminas\Hydrator\ClassMethodsHydrator as LaminasClassMethodsHydrator;

class ClassMethodsHydrator extends LaminasClassMethodsHydrator {


    /**
     * Hydrate an object by populating getter/setter methods
     *
     * Hydrates an object by getter/setter methods of the object.
     *
     * {@inheritDoc}
     */
    public function hydrate(array $data, object $object)
    {
        $objectClass = get_class($object);
        var_dump('toni');
        foreach ($data as $property => $value) {

            $propertyFqn = $objectClass . '::$' . $property;

            if (! isset($this->hydrationMethodsCache[$propertyFqn])) {
                $setterName = 'set' . ucfirst($this->hydrateName($property, $data));

                
                $this->hydrationMethodsCache[$propertyFqn] = is_callable([$object, $setterName])
                    && (! $this->methodExistsCheck || method_exists($object, $setterName))
                    ? $setterName
                    : false;
            }

            if ($this->hydrationMethodsCache[$propertyFqn]) {
                if($this->hydrationMethodsCache[$propertyFqn] === 'setNew') {
                    $value = (int) $value;
                 
                }
         
                
                $object->{$this->hydrationMethodsCache[$propertyFqn]}($this->hydrateValue($property, $value, $data));
            }
        }

        return $object;
    }

}