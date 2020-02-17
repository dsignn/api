<?php
namespace App\Middleware;

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
    public static $CONTENT_TYPE = 'Content-type';

    public static $ACCEPT = 'Accept';

    /**
     * @var array
     */
    protected $acceptFilter = ["/.*\/.*/"];

    /**
     * @var array
     */
    protected $contentTypeFilter = ['/.*\/.*/'];

    /**
     * @var array
     */
    protected $contentTypeStrategy = [];

    /**
     * @var array
     */
    protected $acceptStrategy = [];

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        var_dump($request->getAttribute('route')->getPattern());
        var_dump($request->getMethod());
        die();
        if(!$this->checkAcceptHeader($request)) {

            $response = new ResponseSlim();
            return $response->withStatus(406);
        };

        if(!$this->checkContentTypeHeader($request)) {

            $response = new ResponseSlim();
            return $response->withStatus(415);
        };

        return $handler->handle($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return boolean
     */
    protected function checkAcceptHeader(Request $request) {

        $header = $request->getHeaderLine(ContentNegotiationMiddleware::$ACCEPT);

        if (!$header) {
            return true;
        }

        $check = true;
        for ($cont = 0; $cont < count($this->acceptFilter); $cont++) {
            $check = preg_match( $this->acceptFilter[$cont], $header);

            if (!$check) {
                break;
            }
        }

        return $check;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return boolean
     */
    protected function checkContentTypeHeader(Request $request) {

        $header = $request->getHeaderLine(ContentNegotiationMiddleware::$CONTENT_TYPE);

        if (!$header) {
            return true;
        }

        $check = false;
        for ($cont = 0; $cont < count($this->contentTypeFilter); $cont++) {
            $check = preg_match($this->contentTypeFilter[$cont], $header);

            if (!$check) {
                break;
            }
        }

        return $check;
    }
}