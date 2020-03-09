<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\Accept;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Interface AcceptTransformInterface
 * @package App\Middleware\ContentNegotiation\Accept
 */
interface AcceptTransformInterface {

    /**
     * @param Request $request
     * @return Request
     */
    public function transformAccept(Request $request): Request;
}