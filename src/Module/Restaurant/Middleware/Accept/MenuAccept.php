<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Middleware\Accept;

use App\Application\Request\RequestAwareInterface;
use App\Middleware\ContentNegotiation\Accept\AcceptTransformInterface;
use App\Middleware\ContentNegotiation\Accept\JsonAccept;
use Laminas\Hydrator\HydratorAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Stream;
use Slim\Views\Twig;

/**
 * Class MenuAccept
 * @package App\Module\Restaurant\Middleware\Accept
 */
class MenuAccept implements AcceptTransformInterface, RequestAwareInterface {

    use HydratorAwareTrait;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var string
     */
    protected $jsPath;

   /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * MenuAccept constructor.
     * @param Twig $twig
     * @param ContainerInterface $container
     */
    public function __construct(Twig $twig, ContainerInterface $container) {
        $this->twig = $twig;
        $this->jsPath = $container->get('settings')['twig']['path-js'];
        $this->rootPath = $container->get('settings')['twig']['rootPath'];
    }


    public function transformAccept(Response $response, $data): Response {

        $header = $this->request->getHeaderLine('Accept');

        switch (true) {
            case strpos($header, 'application/json') !== false:
                $jsonAccept = new JsonAccept();
                $jsonAccept->setHydrator($this->getHydrator());

                if ($this->request->getHeaderLine('error-message')) {
                    $body = new Stream(fopen('php://temp', 'r+'));
                    $body->write($json = json_encode(['error-message' => $this->request->getHeaderLine('error-message')]));

                    return $response->withStatus(404)
                        ->withHeader('Content-Type', 'application/json')
                        ->withBody($body);
                }

                return $jsonAccept->transformAccept($response, $data);
            case strpos($header, 'text/html') !== false:

                if ($this->request->getHeaderLine('error-message')) {
                    return $this->renderErrorTwigData($response);
                }

                return $this->renderTwigData($response, $data);

        }
    }

    /**
     * @param $response
     * @param $data
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderTwigData(Response $response, $data) {
        return $this->twig->render(
            $response,
            'restaurant-menu-index.html',
            [
                'base_url' => $this->jsPath,

                'menu' => $data
            ]
        );
    }

    /**
     * @param $response
     * @param $data
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderErrorTwigData(Response $response) {
        return $this->twig->render(
            $response,
            'restaurant-404.html',
            [
                'base_url' => $this->rootPath,
                'error_message' => $this->request->getHeaderLine('error-message')
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function setRequest(ServerRequestInterface $request) {
        $this->request = $request;
    }
}