<?php
declare(strict_types=1);

namespace App\Module\Organization\Validator;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Storage\StorageInterface;
use Exception;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;
use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;

/**
 * Class OrganizationSaveValidator
 * @package App\Module\Organization\Validator
 */
class OrganizationSaveValidator extends AbstractValidator implements ValidatorInterface {

    const ALREADY_EXIST = 'found';

    const OBJECT_ID_NOT_EXIST = 'object-not-found';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::ALREADY_EXIST => 'Name "%name%" organization already exist',
        self::OBJECT_ID_NOT_EXIST => 'Organization object id "%objectId%" not found'
    ];

    /**
     * @var array
     */
    protected $messageVariables = [
        'name' => ['options' => 'name'],
        'objectId' => ['options' => 'objectId'],
    ];

    protected $options = [
        'name' => null,
        'objectId' => null
    ];

    /**
     * @var Request
     */
    protected $container;

    /**
     * @var bool
     */
    protected $findIdInRequest = false;

    /**
     * @var OrganizationEntity
     */
    protected $entity = null;

    /**
     * @var StorageInterface
     */
    protected StorageInterface $storage;

    /**
     * OrganizationSaveValidator constructor.
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
     * @return bool|void
     */
    public function isValid($value, ?iterable $context = null) {

        $this->setValue($value);
        $isValid = true;
        $mongoId = null;
        $nameOrganization = null;

        try {
            $mongoId = new ObjectId($this->getValue());
            
        } catch (Exception $e) {

            $nameOrganization = $this->getValue();
        }

        if($mongoId) {

            $this->options['objectId'] = $this->getValue();
            $mongoEntity = $this->storage->get($this->getValue());

            if (!$mongoEntity) {
                $this->error(self::OBJECT_ID_NOT_EXIST);
                $isValid =  false;
            }
     
        } else {

            $this->options['name'] = $nameOrganization;
            $nameEntity = $this->storage->getAll(['name'=> $nameOrganization ])->current();

            if ($nameEntity) {
                $this->error(self::ALREADY_EXIST);
                $isValid =  false;
            }
        }

        return $isValid;
    }

    /**
     * @return bool
     */
    protected function excludeCurrentNameEntity() {
        return !$this->entity || $this->entity->getName() !== $this->getValue();
    }

    /**
     * @return bool
     */
    public function isFindIdInRequest(): bool {
        return $this->findIdInRequest;
    }

    /**
     * @param bool $findIdInRequest
     * @return OrganizationSaveValidator
     */
    public function setFindIdInRequest(bool $findIdInRequest): OrganizationSaveValidator {
        $this->findIdInRequest = $findIdInRequest;
        return $this;
    }
}