<?php
declare(strict_types=1);

namespace App\Validator\Mongo;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Laminas\Validator\ValidatorInterface;
use MongoDB\BSON\ObjectId;

/**
 * Class ObjectIdValidator
 * @package App\Validator\Mongo
 */
class ObjectIdValidator extends AbstractValidator implements ValidatorInterface {

    /**#@+
     * Validity constants
     * @var string
     */
    const INVALID = 'mongoIdInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID      => 'Invalid mongo id'
    ];

    /**
     * @inheritDoc
     */
    public function isValid($value) {

        $isValid = true;
        try {
            new ObjectId($value);
        } catch (\Exception $e) {
            $this->error(self::INVALID);
            $isValid = false;
        }
        return $isValid;
    }
}