<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use App\Storage\Entity\EntityInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorAwareTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Stream;


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