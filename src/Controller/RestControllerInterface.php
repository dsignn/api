<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RestControllerInterface
 * @package App\Controller
 */
interface RestControllerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function get(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function post(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function put(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function patch(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function paginate(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function options(Request $request, Response $response);
}