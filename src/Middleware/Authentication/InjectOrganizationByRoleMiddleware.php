<?php
declare(strict_types=1);

namespace App\Middleware\Authentication;

use App\Module\Organization\Entity\OrganizationEntity;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthenticationMiddleware
 * @package App\Middleware
 */
class InjectOrganizationByRoleMiddleware implements Middleware {

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        
        $user = $request->getAttribute('app-user');

        switch(true) {
            case !$user:
            case $user->getRoleId() === 'admin':
                return $handler->handle($request);
                break;
        }
       
        $org = $this->recoverFromAuth($request);

        if ($org) { 
            $bodyData = $request->getAttribute('app-body-data');
            if (!$bodyData) {
                $bodyData = [];
            }

            $bodyData['organizationReference'] = [
               'id' => $org->getId(),
               'collection' => 'organization'
            ];

            $queryString = $request->getAttribute('app-query-string');
            if (!$queryString) {
                $queryString = [];
            }

            $queryString['organization_id'] = $org->getId();
        }

        return $handler->handle(
            $request->withAttribute('app-body-data', $bodyData)
                ->withAttribute('app-query-string', $queryString)
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|OrganizationEntity
     */
    protected function recoverFromAuth(ServerRequestInterface $request) { 
        $user = $request->getAttribute('app-user');
        $organization = null;
   
        if ($user && count($user->getOrganizations()) > 0) {
            $organization = $user->getOrganizations()[0];
        }

        return $organization;
    }
}