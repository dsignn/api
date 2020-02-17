<?php
declare(strict_types=1);

namespace App\Module\User\Controller;


use App\Controller\RestController;
use App\Module\User\Storage\UserStorageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UserController
 * @package App\Module\User\Controller
 */
class UserController extends RestController {

    /**
     * @inheritDoc
     */
    public function __construct(UserStorageInterface $storage) {
        parent::__construct($storage);
    }
}