<?php
declare(strict_types=1);

namespace App\Middleware\Authorization;

use App\Auth\RoleInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\GenericRole;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;

/**
 * Class AuthorizationMiddleware
 * @package App\Middleware\Authorization
 */
class AuthorizationMiddleware implements Middleware {

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * AuthorizationMiddleware constructor.
     * @param array $settings
     */
    public function __construct(array $settings = []) {
        $this->settings = $settings;
        $this->acl = new Acl();

        /** TODO add storage and add to it */
        $this->acl->addRole(
            new GenericRole('guest')
        )->addRole(
            new GenericRole('admin')
        );
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // RoleInterface ResourceInterface privilege
        $role = $request->getAttribute('app-user') ? $request->getAttribute('app-user') : new GenericRole('guest');
        $resource = new GenericResource($request->getAttribute('__route__')->getPattern());
        $method = $request->getMethod();

        if ($method === 'OPTIONS') {
            return $handler->handle($request);
        }

        $this->acl->addResource($resource);
        $this->loadPermission($resource, $request);

        if (!$this->acl->isAllowed($role, $resource, $method)) {
            throw new HttpException(
                $request,
                sprintf('Unauthorized role %s for the resource %s method %s ', $role->getRoleId(), $resource->getResourceId(), $method),
                401
            );
        }

        return $handler->handle($request);
    }

    protected function loadPermission(ResourceInterface $resource, ServerRequestInterface $request) {

        /** @var $role RoleInterface */
        $method = $request->getMethod();
        if (isset($this->settings[$resource->getResourceId()])) {

            foreach ($this->settings[$resource->getResourceId()] as $role => $list) {

                if (!$this->acl->hasRole($role)) {
                    throw new \Exception(sprintf('Role %s not found', $role));
                }

                $isAllowed = isset($list['allow']) && $list['allow'] === true ? "allow" : "deny";
                $this->acl->{$isAllowed}($role, $resource);
                if (isset($list['privileges']) && is_array($list['privileges'])) {

                    foreach ($list['privileges'] as $privilegeItem) {
                        if (isset($privilegeItem['method']) && $privilegeItem['method'] === $method) {
                            $isAllowed = isset($privilegeItem['allow']) && $privilegeItem['allow'] === true ? 'allow' : 'deny';
                            $this->acl->{$isAllowed}($role, $resource, $method);;
                        }
                    }
                }
            }
        }
    }
}