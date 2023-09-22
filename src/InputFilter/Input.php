<?php
declare(strict_types=1);

namespace App\InputFilter;

use Laminas\InputFilter\Input as AppInput;

/**
 * Class Input
 * @package App\InputFilter
 */
class Input extends AppInput {

    /**
     * @inheritDoc
     */
    public function isValid($context = null) {

        if (is_array($this->errorMessage)) {
            $this->errorMessage = null;
        }

        $value           = $this->getValue();
        $hasValue        = $this->hasValue();
        $required        = $this->isRequired();

        if (!$hasValue && !$required) {
            return true;
        }

        $validator = $this->getValidatorChain();
        $result    = $validator->isValid($value, $context);
        if (! $result && $this->hasFallback()) {
            $this->setValue($this->getFallbackValue());
            $result = true;
        }

        return $result;
    }
}