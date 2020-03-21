<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\Accept;

use Laminas\Hydrator\HydratorAwareInterface;
use Psr\Http\Message\ResponseInterface as Response;
/**
 * Interface AcceptTransformInterface
 * @package App\Middleware\ContentNegotiation\Accept
 */
interface AcceptTransformInterface extends HydratorAwareInterface {

    /**
     * @param Response $response
     * @param $data
     * @return Response
     */
    public function transformAccept(Response $response, $data): Response;
}