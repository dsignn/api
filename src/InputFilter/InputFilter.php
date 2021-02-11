<?php
declare(strict_types=1);

namespace App\InputFilter;

use Laminas\InputFilter\InputFilter as LaminaInputFilter;
use Laminas\InputFilter\InputFilterInterface;

class InputFilter extends LaminaInputFilter {

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
        $inputs = $this->validationGroup ?: array_keys($this->inputs);
        $values = [];
        foreach ($inputs as $name) {
            $input = $this->inputs[$name];

            if ($input instanceof InputFilterInterface) {
                $data = $input->getValues();
                if (in_array($name, $this->propertyToRemove) && empty($data)) {
                    continue;
                }
                $values[$name] = $data;
                continue;
            }

            $value = $input->getValue();
            if (in_array($name, $this->propertyToRemove) && !$value) {
                continue;
            }

            $values[$name] = $value;
        }
        return $values;
    }
}