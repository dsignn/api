<?php
declare(strict_types=1);

namespace App\Module\Device\Controller;

use App\Controller\AcceptTrait;
use App\Controller\RpcControllerInterface;
use App\Module\Device\Storage\DeviceStorageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DateTime;

/**
 * Class DeviceController
 * @package App\Module\Device\Controller
 */
class DeviceUpsertRpcRestController implements RpcControllerInterface {

    use AcceptTrait;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * ActivationToken constructor.
     * @param UserStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(DeviceStorageInterface $storage) {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function rpc(Request $request, Response $response) {

        $data = $request->getParsedBody();

        if ($request->getAttribute('app-organization')) {
            $data['organization_reference'] = $request->getAttribute('app-organization')->getId();
        }

        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);


            if (!$validator->isValid()) {
                $response = $response->withStatus(422);
                return $this->getAcceptData($request, $response, ['errors' => $validator->getMessages()]);
            }
        }

     
        $data = array_merge($data, $validator->getValues());
        $entity = $this->storage->get($data['id']);
   
     
        if (!$entity) {
            $entity = $this->storage->getEntityPrototype()->getPrototype($data);
            $entity->setCreatedDate(new DateTime());
        } 

        $this->storage->getHydrator()->hydrate($data, $entity); 
        $entity->setLastUpdateDate(new DateTime());

        $this->storage->update($entity);
        
        return $this->getAcceptData($request, $response, $entity);
    }
}