<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation;

use App\Middleware\ContentNegotiation\Accept\AcceptTransformInterface;
use App\Middleware\ContentNegotiation\Exception\ServiceNotFound;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Trait AcceptServiceAwareTrait
 * @package App\Middleware\ContentNegotiation
 */
trait AcceptServiceAwareTrait {

    /**
     * Add in the class where extends
     *
     * protected $hydratorService;
     */

    /**
     * @param Request $request
     * @return AcceptTransformInterface
     * @throws ServiceNotFound
     */
    protected function getAcceptService(Request $request) {

        /** @var AcceptTransformInterface $acceptService */
        $acceptService = $request->getAttribute('AcceptService');

        if (!$acceptService) {
            throw new ServiceNotFound('ContentTypeService not found in request attribute');
        }

        if ($this->container->has($this->hydratorService)) {
            $acceptService->setHydrator($this->container->get($this->hydratorService));
        }

        return $acceptService;
    }
}