<?php
declare(strict_types=1);

namespace App\Module\Monitor\Controller;

use App\Controller\RestController;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Class MonitorController
 * @package App\Module\Monitor\Controller
 */
class MonitorController extends RestController
{
    /**
     * @var MonitorStorageInterface
     */
    protected $storage;

    /**
     * MonitorController constructor.
     * @param MonitorStorageInterface $storage
     */
    public function __construct(MonitorStorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function get(Request $request, Response $response) {
        $response->getBody()->write(json_encode(["get" => "monitor item"]));
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getAll(Request $request, Response $response) {
        $response->getBody()->write(json_encode(["get" => "monitor list"]));
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response|void

    public function post(Request $request, Response $response) {
        // TODO attach to content negotiation middleware
        $body = json_decode($request->getBody()->getContents(), true);
        $data = $this->storage->save($body);
        $streamFactory = new StreamFactory();
        return $response->withStatus(201)->withBody($streamFactory->createStream(json_encode($data)));
    }
     */
}