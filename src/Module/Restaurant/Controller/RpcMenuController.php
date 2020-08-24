<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RpcMenuController
 * @package App\Module\Restaurant\Controller
 */
class RpcMenuController implements RpcControllerInterface {

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $slug = $request->getAttribute('__route__')->getArgument('slug');
        // TODO: Implement rpc() method.
        var_dump($slug);
        die();
    }
}