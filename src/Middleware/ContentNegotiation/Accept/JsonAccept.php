<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\Accept;

use Psr\Http\Message\ServerRequestInterface as Request;
use function DI\value;

/**
 * Class JsonAccept
 * @package App\Middleware\ContentNegotiation\Accept
 */
class JsonAccept implements AcceptTransformInterface {

    /**
     * @inheritDoc
     */
    public function transformAccept(Request $request): Request {
        return $request->withParsedBody(json_decode($request->getBody()->getContents(), true));
    }
}