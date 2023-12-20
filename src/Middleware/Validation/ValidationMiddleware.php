<?php
declare(strict_types=1);

namespace App\Middleware\Validation;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ValidationMiddleware
 * @package App\Middleware\Validation
 */
class ValidationMiddleware implements Middleware {

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ValidationMiddleware constructor.
     * @param $setting
     * @param ContainerInterface $container
     */
    public function __construct($setting, ContainerInterface $container) {
        $this->settings = $setting;
        $this->container = $container;
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $path = $request->getAttribute('__route__')->getPattern();
        $method = $request->getMethod();

        if (!$this->hasValidationFilter($path, $method)) {
            return $handler->handle($request);
        }

        $request = $request->withAttribute('app-validation', $this->getValidationFilter($path, $method));
        $this->container->set('Request', $request);

        return $handler->handle($request);
    }

    /**
     * @param $path
     * @param $method
     * @return bool
     */
    protected function hasValidationFilter($path, $method) {
        return isset($this->settings[$path]) && isset($this->settings[$path][$method]);
    }

    /**
     * @param $path
     * @param $method
     * @return mixed
     */
    protected function getValidationFilter($path, $method) {
        $service = null;
        if ($this->hasValidationFilter($path, $method)) {
            $serviceName = $this->settings[$path][$method];
            if ($this->container->has($serviceName)) {
                $service = $this->container->get($serviceName);
            }
        }
        return $service;
    }
}