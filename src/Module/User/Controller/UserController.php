<?php
declare(strict_types=1);

namespace App\Module\User\Controller;


use App\Controller\RestController;
use App\Module\User\Storage\UserStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class UserController
 * @package App\Module\User\Controller
 */
class UserController extends RestController {

    /**
     * @var string
     */
    protected $hydratorService = 'RestUserEntityHydrator';

    /**
     * @inheritDoc
     */
    public function __construct(UserStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}