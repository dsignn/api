<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Validator;

use App\Module\Restaurant\Entity\MenuEntity;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;

/**
 * Class StatusDateValidator
 * @package App\Module\Restaurant\Validator
 */
class StatusDateValidator extends AbstractValidator implements ValidatorInterface {

    /**
     * @var string
     */
    const EMPTY = 'empty';

    /**
     * @var string
     */
    const INVALID_DATE = 'invalid_date';

    /**
     * @var string
     */
    protected $format = 'd-m-Y';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::EMPTY      => "Value can't be empty",
        self::INVALID_DATE      => "Invalid value, the format must be a date",
    ];

    /**
     * @inheritDoc
     */
    public function isValid($value, $context = null) {

        $isValid = true;
        $this->setOptions(["format" => $this->getFormat()]);


        if (is_array($context) && $context['type'] && $context['type'] === MenuEntity::TYPE_DAILY) {

            switch (true) {
                case !$value === true:
                    $isValid = false;
                    $this->error(self::EMPTY);
                    break;
                case $value instanceof \DateTime !== true:
                    $isValid = false;
                    $this->error(self::INVALID_DATE);
                    break;
            }
        }

        return $isValid;
    }

    /**
     * @return string
     */
    public function getFormat(): string {
        return $this->format;
    }

    /**
     * @param string $format
     * @return StatusDateValidator
     */
    public function setFormat(string $format): StatusDateValidator {
        $this->format = $format;
        return $this;
    }
}