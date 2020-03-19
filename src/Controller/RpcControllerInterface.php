<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RpcControllerInterface
 * @package App\Controller
 */
interface RpcControllerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function rpc(Request $request, Response $response);
}