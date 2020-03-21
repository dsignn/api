<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation;

use App\Middleware\ContentNegotiation\ContentType\ContentTypeTransformInterface;
use App\Middleware\ContentNegotiation\Exception\ServiceNotFound;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Trait ContentTypeAwareTrait
 * @package App\Middleware\ContentNegotiation
 */
trait ContentTypeAwareTrait {

    /**
     * Add in the class where extends
     *
     * protected $hydratorService;
     */


    /**
     * @param Request $request
     * @return ContentTypeTransformInterface
     * @throws ServiceNotFound
     */
    protected function getContentTypeService(Request $request) {

        /** @var ContentTypeTransformInterface $contentTypeService */
        $contentTypeService = $request->getAttribute('ContentTypeService');

        if (!$contentTypeService) {
            throw new ServiceNotFound('ContentTypeService not found in request attribute');
        }

        if ($this->container->has($this->hydratorService)) {
            $contentTypeService->setHydrator($this->container->get($this->hydratorService));
        }

        return $contentTypeService;
    }
}