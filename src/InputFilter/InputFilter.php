<?php
declare(strict_types=1);

namespace App\InputFilter;

use Laminas\InputFilter\BaseInputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputInterface;
use Slim\App;

class InputFilter extends BaseInputFilter {

    /**
     * @var array
     */
    protected $propertyToRemove = [];

    /**
     * @param array $proprieties
     * @return InputFilter
     */
    public function addPropertiesIfEmpty(array $proprieties): InputFilter {
        $this->propertyToRemove = $proprieties;
        return $this;
    }

    /**
     * @param $name
     * @return InputFilter
     */
    public function addPropertyIfEmpty(string $name): InputFilter {
        array_push($this->propertyToRemove, $name);
        return $this;
    }

    /**
     * @param $name
     * @return InputFilter
     */
    public function removePropertyIfEmpty(string $name): InputFilter {
        if($this->propertyToRemove[$name]) {
            unset($this->propertyToRemove[$name]);
        }
        return $this;
    }

    /**
     * Return a list of filtered values
     *
     * @return array
     */
    public function getValues(): array {
        return $this->getValuesWrapper($this->getData(), $this);
    }

    /**
     *
     * @param array $data
     * @param BaseInputFilter $inputFilter
     * @return array
     */
    public function getValuesWrapper(array $data, InputFilter $inputFilter): array {

        $inputs = $this->validationGroup ?: array_keys($this->inputs);
        $values = [];
       // $data = $this->getData();
       foreach ($inputs as $name) {
            
            $input = $this->inputs[$name];
            
            if ($input instanceof InputFilterInterface) {

                $value = $input->getValues();
                if (in_array($name, $this->propertyToRemove) || !isset($data[$name])) {
                    continue;
                }
    
                $values[$name] = $input->getValues();
            } elseif ($input instanceof InputInterface) {

                $value = $input->getValue();           
                if (in_array($name, $this->propertyToRemove) && !$value) {
                    continue;
                }
                $values[$name] = $value;
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }
}