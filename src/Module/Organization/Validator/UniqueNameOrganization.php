<?php
declare(strict_types=1);

namespace App\Module\Organization\Validator;

use App\Module\Organization\Url\GenericSlugify;
use App\Module\Organization\Url\SlugifyInterface;
use App\Storage\StorageInterface;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\ValidatorInterface;

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
        self::ALREADY_EXIST => "Name organization already exist'"
    ];

    /**
     * @var SlugifyInterface
     */
    protected $slugifyService;

    /**
     * EmailExistValidator constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage, SlugifyInterface $slugify = null) {
        $this->storage = $storage;
        $this->slugifyService = $slugify ? $slugify : new GenericSlugify();
        parent::__construct([]);
    }

    /**
     * @param mixed $value
     * @return bool|void
     */
    public function isValid($value) {

        $this->setValue($value);
        // TODO Configurable

        $normalize = $this->slugifyService->slugify($this->getValue());
        $isValid = true;
        $resultSet = $this->storage->getAll(['normalize_name'=> $normalize ]);

        if ($resultSet->count() > 0) {
            $this->error(self::ALREADY_EXIST);
            $isValid =  false;
        }
        return $isValid;
    }



}