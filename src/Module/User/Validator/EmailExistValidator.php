<?php
declare(strict_types=1);

namespace App\Module\User\Validator;

use App\Module\User\Entity\UserEntity;
use App\Storage\StorageInterface;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;
use MongoDB\BSON\ObjectId;

/**
 * Class EmailExistValidator
 * @package App\Module\User\Validator
 */
class EmailExistValidator extends AbstractValidator {

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
     * @param null $context
     * @return bool
     */
    public function isValid($value, $context = null) {


        $this->setValue($value);
        $isValid = true;

        $resultSet = $this->storage->getAll(['email'=> $value ]);
        $user = $this->_extractUserFromContext($context);

        if (($resultSet->count() > 0 && !$user) || ($resultSet->count() > 0 && $user && $user->getEmail() !== $value)) {
            $this->error(self::FOUND);
            $isValid =  false;
        }

        return $isValid;
    }

    /**
     * @param null $context
     * @return UserEntity|null
     */
    protected function _extractUserFromContext($context = null) {
        $user = null;
        if ($context && is_array($context) && isset($context['id'])) {

            try {
                $user = $this->storage->get($context['id']);
            } catch (\Exception $exception) {
                // TODO add log????
            }
        }
        return $user;
    }
}