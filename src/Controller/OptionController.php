<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class OptionController
 * @package App\Controller
 */
final class OptionController {

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function options(Request $request, Response $response) {
        return $response;
    }
}