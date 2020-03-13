<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use Psr\Http\Message\ResponseInterface as Response;
use Laminas\Hydrator\HydratorAwareInterface;

/**
 * Interface ContentTypeTransformInterface
 * @package App\Middleware\ContentNegotiation\ContentType
 */
interface ContentTypeTransformInterface extends HydratorAwareInterface {

    /**
     * @param Response $response
     * @param $data
     * @return Response
     */
    public function transformContentType(Response $response, $data): Response;
}