<?php
declare(strict_types=1);

namespace App\Module\Machine\Controller;

use App\Controller\RpcControllerInterface;
use App\Module\Machine\Storage\MachineStorageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use DateTime;

/**
 * Class MachineController
 * @package App\Module\Machine\Controller
 */
class MachineUpsertRpcRestController implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * ActivationToken constructor.
     * @param UserStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MachineStorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function rpc(Request $request, Response $response) {

        $data = $request->getParsedBody();

      
        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);

            if (!$validator->isValid()) {
                $acceptService = $this->getAcceptService($request);
                $response = $acceptService->transformAccept(
                    $response,
                    ['errors' => $validator->getMessages()]
                );
                return $response->withStatus(422);
            }
        }


        $entity = $this->storage->get($data['id']);
   
        if (!$entity) {
            $entity = $this->storage->getEntityPrototype()->getPrototype($data);
            $entity->setCreatedDate(new DateTime());
        } 

        $this->storage->getHydrator()->hydrate($data, $entity); 
        $entity->setLastUpdateDate(new DateTime());

        $this->storage->update($entity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }
}