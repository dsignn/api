<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function GuzzleHttp\default_ca_bundle;

/**
 * @trait AcceptTrait
 */
trait AcceptTrait { 

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     */
    protected function getAcceptData(Request $request, Response $response, $entity) {
   
        $acceptService = $request->getAttribute('app-accept-service');
        switch(true) {
                case !!$acceptService:
             
                    return $acceptService->transformAccept($response, $entity);
                    break;
                
                default:
                    return $response->withStatus(200);
                    break;
        }
    }
}