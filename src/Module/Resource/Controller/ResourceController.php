<?php
declare(strict_types=1);

namespace App\Module\Resource\Controller;

use App\Controller\RestController;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ResourceController
 * @package App\Module\Resource\Controller
 */
class ResourceController  {

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * ResourceController constructor.
     * @param MonitorStorageInterface $storage
     */
    public function __construct(MonitorStorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function post(Request $request, Response $response) {
        $body = $request->getBody();

        var_dump($request->getParsedBody());
        var_dump($_POST);
//var_dump($files);
        die();
    }
}