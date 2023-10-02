<?php
declare(strict_types=1);

namespace App\Module\Organization\Validator;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Url\GenericSlugify;
use App\Module\Organization\Url\SlugifyInterface;
use App\Storage\StorageInterface;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;

/**
 * Class UniqueNameOrganization
 * @package App\Module\Organization\Validator
 */
class UniqueNameOrganization extends AbstractValidator implements ValidatorInterface {

    const ALREADY_EXIST = 'found';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::ALREADY_EXIST => "Name organization already exist"
    ];

    /**
     * @var SlugifyInterface
     */
    protected $slugifyService;

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
     * UniqueNameOrganization constructor.
     * @param StorageInterface $storage
     * @param ContainerInterface $container
     * @param SlugifyInterface|null $slugify
     */
    public function __construct(StorageInterface $storage, ContainerInterface $container, SlugifyInterface $slugify = null) {
        $this->storage = $storage;
        $this->container = $container;
        $this->slugifyService = $slugify ? $slugify : new GenericSlugify();
        parent::__construct([]);
    }

    /**
     * @param mixed $value
     * @return bool|void
     */
    public function isValid($value, ?iterable $context = null) {

        $this->setValue($value);

        if ($this->findIdInRequest) {
            $route = $this->container->get('Request')->getAttribute('__route__');
            $id =  $route->getArgument('id');
            if ($id) {
                $this->entity = $this->storage->get($id);
            }
        }

        $normalize = $this->slugifyService->slugify($this->getValue());
        $isValid = true;
        $resultSet = $this->storage->getAll(['normalize_name'=> $normalize ]);

        if ($resultSet->count() > 0 && $this->excludeCurrentNameEntity()) {
            $this->error(self::ALREADY_EXIST);
            $isValid =  false;
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
     * @return UniqueNameOrganization
     */
    public function setFindIdInRequest(bool $findIdInRequest): UniqueNameOrganization {
        $this->findIdInRequest = $findIdInRequest;
        return $this;
    }
}