<?php
declare(strict_types=1);

namespace App\Middleware\QueryString;

use App\Module\Monitor\Http\QueryString\MonitorQueryString;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class QueryStringMiddleware
 * @package App\Middleware\QueryString
 */
class QueryStringMiddleware implements Middleware {

    /**
     * @var string
     */
    public static $SETTING_KEY = 'queryString';

    /**
     * @var [ContainerInterface]
     */
    protected $container;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        
        $this->container = $container;

        $this->loadSetting();
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $path = $request->getAttribute('__route__')->getPattern();
        $method = $request->getMethod();

        $setting = $this->getSetting($path, $method);
      
        if ($setting && isset($setting["service"]) && is_string($setting["service"]) && $this->container->has($setting["service"])) {
             
            $request = $request ->withAttribute(
                'app-storage-filter', 
                $this->container->get($setting["service"])
            );
        }

        return $handler->handle($request);
    }

    /**
     * @return void
     */
    protected function loadSetting() {
        $this->settings = isset($this->container->get('settings')[QueryStringMiddleware::$SETTING_KEY]) ? $this->container->get('settings')[QueryStringMiddleware::$SETTING_KEY] : [];
    }

    /**
     * @param $path
     * @param $method
     * @return array
     */
    protected function getSetting($path, $method) {

        $defaultSettings = [];
        $methodSettings = [];

        if (isset($this->settings[$path]['default'])) {
            $defaultSettings = $this->settings[$path]['default'];
        }

        if (isset($this->settings[$path][$method])) {
            $methodSettings = $this->settings[$path][$method];
        }

        return array_merge($defaultSettings, $methodSettings);
    }
}