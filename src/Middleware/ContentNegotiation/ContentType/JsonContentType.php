<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use Psr\Http\Message\ServerRequestInterface as Request;


/**
 * Class JsonContentType
 * @package App\Middleware\ContentNegotiation\ContentType
 */
class JsonContentType implements ContentTypeTransformInterface {

    /**
     * @inheritDoc
     */
    public function transformContentType(Request $request): Request {
        return $request->withParsedBody(json_decode($request->getBody()->getContents(), true));
    }
}