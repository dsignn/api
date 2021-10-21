<?php
declare(strict_types=1);

namespace App\InputFilter;

use Laminas\InputFilter\BaseInputFilter;
use Laminas\InputFilter\InputFilterInterface;
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
        $values = [];
        $data = $this->getData();
        foreach($data as $key => $value) {

            if (!$inputFilter->has($key)) {
                continue;
            }
            
            if ($key === 'activationCode') {
                die();
            }
            $input = $inputFilter->get($key);
            
            if ($input instanceof InputFilter && is_array($value)) {
                $values[$key] = $this->getValuesWrapper($value, $input);
            } else {

                $value = $input->getValue();
                if (in_array($key, $this->propertyToRemove) && !$value) {
                    var_dump($key);
                    die();
                    continue;
                }
                $values[$key] = $value;
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