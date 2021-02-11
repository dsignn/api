<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation;

use App\Application\Request\RequestAwareInterface;
use App\Middleware\ContentNegotiation\Accept\AcceptTransformInterface;
use App\Middleware\ContentNegotiation\Exception\ServiceNotFound;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Trait AcceptServiceAwareTrait
 * @package App\Middleware\ContentNegotiation
 */
trait AcceptServiceAwareTrait {

    protected $container;

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
            throw new ServiceNotFound('AcceptService not found in request attribute');
        }

        if (property_exists($this, 'hydratorService') && $this->container->has($this->hydratorService)) {
            $acceptService->setHydrator($this->container->get($this->hydratorService));
        }

        if ($acceptService && $acceptService instanceof RequestAwareInterface) {
            $acceptService->setRequest($request);
        }

        return $acceptService;
    }
}