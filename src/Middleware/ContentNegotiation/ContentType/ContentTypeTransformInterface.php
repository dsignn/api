<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Interface ContentTypeTransformInterface
 * @package App\Middleware\ContentNegotiation\ContentType
 */
interface ContentTypeTransformInterface {

    /**
     * @param Request $request
     * @return Request
     */
    public function transformContentType(Request $request): Request;
}