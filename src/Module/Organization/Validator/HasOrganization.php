<?php
declare(strict_types=1);

namespace App\Module\Organization\Validator;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Storage\StorageInterface;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use MongoDB\BSON\ObjectId;

/**
 * Class HasOrganization
 * @package App\Module\Organization\Validator
 */
class HasOrganization extends AbstractValidator implements ValidatorInterface {

    const NOT_EXIST = 'not_exist';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_EXIST => "Organization not exist"
    ];

    /**
     * @var Request
     */
    protected $container;

    /**
     * @var OrganizationEntity
     */
    protected $entity = null;

    /**
     * HasOrganization constructor.
     * @param StorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(StorageInterface $storage, ContainerInterface $container) {
        $this->storage = $storage;
        $this->container = $container;
        parent::__construct([]);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isValid($value) {

        $this->setValue($value);
        $result = true;

        $resultSet = $this->storage->getAll(['_id'=>  new ObjectId($value)]);
        if ($resultSet->count() < 1) {
            $result = false;
            $this->error(self::NOT_EXIST);
        }
        
        return $result;
    }
}