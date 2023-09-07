<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation;

use App\Middleware\ContentNegotiation\Accept\AcceptContainer;
use App\Middleware\ContentNegotiation\Accept\AcceptTransformInterface;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeContainer;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeTransformInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseSlim;

/**
 * Class ContentNegotiationMiddleware
 * @package App\Middleware
 */
class ContentNegotiationMiddleware implements Middleware
{
    /**
     * @var string
     */
    public static $CONTENT_TYPE = 'Content-type';

    /**
     * @var string
     */
    public static $ACCEPT = 'Accept';

    protected $skipContentTypeMethod = [
        'GET',
        'DELETE',
        'OPTION'
    ];

    /**
     * @var array
     */
    protected $defaultContentTypeServices = [];

    /**
     * @var array
     */
    protected $defaultAcceptServices = [];

    /**
     * @var ContainerInterface
     */
    protected $contentTypeContainer;

    /**
     * @var ContainerInterface
     */
    protected $acceptContainer;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $acceptFilter = ["/.*\/.*/"];

    /**
     * @var array
     */
    protected $contentTypeFilter = ["/.*\/.*/"];

    /**
     * @var bool
     */
    protected $isCustomAccept = false;

    /**
     * @var bool
     */
    protected $isCustomContentType = false;

    /**
     * ContentNegotiationMiddleware constructor.
     * @param $setting
     */
    public function __construct($setting) {
        $this->settings = $setting;
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response {

        $path = $request->getAttribute('__route__')->getPattern();
        $method = $request->getMethod();

        if ($method === 'OPTIONS') {
            return $handler->handle($request);
        }

        $this->loadSettings($path, $method);

        if(!$this->isValidAcceptHeader($request)) {
            return (new ResponseSlim())->withStatus(406);
        };

        if(!$this->isValidContentTypeHeader($request)) {
            return (new ResponseSlim())->withStatus(415);
        };
      
        /** @var AcceptTransformInterface $acceptService */
        $acceptService = $this->getAcceptService($path, $method, $request->getHeaderLine(self::$ACCEPT));

        /** @var ContentTypeTransformInterface $contentTypeService */
        $contentTypeService = $this->getContentTypeService($path, $method, $request->getHeaderLine(self::$CONTENT_TYPE));

        if ($contentTypeService) {
            try {
                $request = $contentTypeService->transformContentType($request);
            } catch (\Exception $e) {
                return (new ResponseSlim())->withStatus(406);
            }
        }

        if ($acceptService) {
            $request = $request->withAttribute(
                'AcceptService',
                $acceptService
            );
        }

        return $handler->handle($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return boolean
     */
    protected function isValidAcceptHeader(Request $request) {

        $header = $request->getHeaderLine(ContentNegotiationMiddleware::$ACCEPT);

        if (!$header) {
            return false;
        }

        $check = false;
        for ($cont = 0; $cont < count($this->acceptFilter); $cont++) {
            $check = preg_match($this->acceptFilter[$cont], $header);
            if ($check) {
                break;
            }
        }
        return $check;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isValidContentTypeHeader(Request $request) {

        $header = $request->getHeaderLine(ContentNegotiationMiddleware::$CONTENT_TYPE);

        if (in_array($request->getMethod(), $this->skipContentTypeMethod)) {
            return true;
        }

        if (!$header) {
            return false;
        }

        $check = true;
        for ($cont = 0; $cont < count($this->contentTypeFilter); $cont++) {
            $check = !!preg_match($this->contentTypeFilter[$cont], $header);
            if (!$check) {
                break;
            }
        }
        return $check;
    }

    /**
     * @param $path
     * @param $method
     */
    protected function loadSettings($path, $method) {

        if (!isset($this->settings[$path]) || (!isset($this->settings[$path]['default']) && !isset($this->settings[$path][$method])) ) {
            return;
        }

        $settings = $this->getSetting($path, $method);

        if (isset($settings['acceptFilter']) && $settings['acceptFilter']) {
            $this->acceptFilter = $settings['acceptFilter'];
            $this->isCustomAccept = true;
        }

        if (isset($settings['contentTypeFilter']) && $settings['contentTypeFilter']) {
            $this->contentTypeFilter = $settings['contentTypeFilter'];
            $this->isCustomContentType = true;
        }
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

    /**
     * @param $path
     * @param $method
     * @param $header
     * @return AcceptTransformInterface|null
     */
    protected function getAcceptService($path, $method, $header) {
        $settings = $this->getSetting($path, $method);
      
        $service = null;
        $default = isset($this->defaultAcceptServices[$header]) ? $this->defaultAcceptServices[$header] : '';
        $custom = isset($settings['acceptService']) ? $settings['acceptService'] : '';

        $serviceName = $custom ? $custom : $default;
     
        if ($this->acceptContainer->has($serviceName)) {
            $service = $this->acceptContainer->get($serviceName);
        }
    
        return $service;
    }

    /**
     * @param $path
     * @param $method
     * @param $header
     * @return ContentTypeTransformInterface|null
     */
    protected function getContentTypeService($path, $method, $header) {

        $settings = $this->getSetting($path, $method);

        $service = null;
        $default = isset($this->defaultContentTypeServices[$header]) ? $this->defaultContentTypeServices[$header] : '';
        $custom = isset($settings['contentTypeService']) ? $settings['contentTypeService'] : '';
        $serviceName = $custom ? $custom : $default;

        if ($this->contentTypeContainer->has($serviceName)) {
            $service = $this->contentTypeContainer->get($serviceName);
        }

        return $service;
    }

    /**
     * @param ContentTypeContainer $container
     * @return ContentNegotiationMiddleware
     */
    public function setContentTypeContainer(ContentTypeContainer $container): ContentNegotiationMiddleware {
        $this->contentTypeContainer = $container;
        return $this;
    }

    /**
     * @param AcceptContainer $container
     * @return ContentNegotiationMiddleware
     */
    public function setAcceptContainer(AcceptContainer $container): ContentNegotiationMiddleware {
        $this->acceptContainer = $container;
        return $this;
    }

    /**
     * @param array $defaultContentTypeServices
     * @return ContentNegotiationMiddleware
     */
    public function setDefaultContentTypeServices(array $defaultContentTypeServices): ContentNegotiationMiddleware  {
        $this->defaultContentTypeServices = $defaultContentTypeServices;
        return $this;
    }

    /**
     * @param array $defaultAcceptServices
     * @return ContentNegotiationMiddleware
     */
    public function setDefaultAcceptServices(array $defaultAcceptServices): ContentNegotiationMiddleware {
        $this->defaultAcceptServices = $defaultAcceptServices;
        return $this;
    }
}