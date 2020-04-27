<?php
declare(strict_types=1);

namespace App\Module\User\Validator;

use App\Storage\StorageInterface;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;

/**
 * Class EmailExistValidator
 * @package App\Module\User\Validator
 */
class EmailExistValidator extends AbstractValidator implements ValidatorInterface {

    const FOUND = 'found';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::FOUND => "The email is already exist'"
    ];

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * EmailExistValidator constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {
        $this->storage = $storage;
        parent::__construct([]);
    }

    /**
     * @param mixed $value
     * @return bool|void
     */
    public function isValid($value) {

        $this->setValue($value);
        $isValid = true;

        $resultSet = $this->storage->getAll(['email'=> $value ]);

        if ($resultSet->count() > 0) {
            $this->error(self::FOUND);
            $isValid =  false;
        }
        return $isValid;
    }
}