<?php
declare(strict_types=1);

namespace App\Validator;

use Laminas\InputFilter\Exception\RuntimeException;
use Laminas\Validator\AbstractValidator;

class ArrayIntersec extends AbstractValidator {

    const NOT_IN_ARRAY = 'notInArray';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_IN_ARRAY => 'The input was not found in the haystack',
    ];

    /**
     * Returns the haystack option
     *
     * @return mixed
     * @throws Exception\RuntimeException if haystack option is not set
     */
    public function getHaystack()
    {
        if ($this->haystack === null) {
            throw new RuntimeException('haystack option is mandatory');
        }
        return $this->haystack;
    }

    /**
     * Sets the haystack option
     *
     * @param  mixed $haystack
     * @return $this Provides a fluent interface
     */
    public function setHaystack(array $haystack)
    {
        $this->haystack = $haystack;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value) {

        $isValid = true;
       
        $this->setValue($value);

        if(is_array($value)) {
            foreach ($value as $content) {
                if (!in_array($content, $this->getHaystack())) {
                    $isValid = false;
                    break;
                }
            }
        } else {
            $isValid = !!in_array($value, $this->getHaystack());
        }

        if (!$isValid) {
            $this->error(self::NOT_IN_ARRAY);
        }

        return $isValid;
    }
}