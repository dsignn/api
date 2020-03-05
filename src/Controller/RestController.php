<?php
declare(strict_types=1);

namespace App\Controller;

use App\Storage\StorageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Hydrator\HydratorAwareInterface;

/**
 * Class RestController
 * @package App\Controller
 */
class RestController
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * RestAbstractController constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function get(Request $request, Response $response) {

        $id = $request->getAttribute('route')->getArgument('id');


        $entity = $this->storage->get($id);
        if ($this->storage instanceof HydratorAwareInterface) {
            $entity = $entity  ? $this->storage->getHydrator()->extract($entity) : null;
        }

        

        $response->getBody()->write(json_encode($entity));
        return $response->withStatus(200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function post(Request $request, Response $response) {

        var_dump($request->getBody()->getContents());
        die();
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function put(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function patch(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function paginate(Request $request, Response $response) {
      //  $result = $this->storage->gelAll([]);
      //  $result->next();

        $query = $request->getQueryParams();
        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;


        $list = $this->storage->getPage($page, $itemPerPage);
       // var_dump($list->count());
        var_dump($list);
        die();
        $response->getBody()->write(json_encode(["get" => "paginate"]));
        return $response;
        return $response->withStatus(405);
    }


}