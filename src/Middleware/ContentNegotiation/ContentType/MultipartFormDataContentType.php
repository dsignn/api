<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use Psr\Http\Message\ServerRequestInterface as Request;


/**
 * Class MultipartFormDataContentType
 * @package App\Middleware\ContentNegotiation\ContentType
 */
class MultipartFormDataContentType implements ContentTypeTransformInterface {

    /**
     * @inheritDoc
     */
    public function transformContentType(Request $request): Request {
        return $request->withParsedBody($_POST);
    }
}