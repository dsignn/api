<?php
declare(strict_types=1);

namespace App\Module\User\Event;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Storage\Entity\Reference;
use App\Storage\Event\PreProcess;
use App\Storage\StorageInterface;
use Exception;
use GuzzleHttp\Client;
use Laminas\EventManager\EventInterface;
use Laminas\Hydrator\HydrationInterface;
use MongoDB\BSON\ObjectId;

/**
 * Class AppendOrganizationEvent
 * @package App\Module\User\Event
 */
class AppendOrganizationEvent {

    /**
     * @var string
     */
    protected $url;

    /**
     * @var HydrationInterface
     */
    protected $clientCredentials = [];

    /**
     * @var HydrationInterface
     */
    protected $hydrator;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var StorageInterface
     */
    protected $organizationStorage;

    /**
     * AppendOrganizationEvent constructor.
     * @param Client $client
     * @param string $url
     * @param HydrationInterface $hydrator
     * @param array $clientCredentials
     */
    public function __construct(Client $client, 
        string $url, 
        HydrationInterface $hydrator, 
        array $clientCredentials = [],
        StorageInterface $organizationStorage) {

        $this->client = $client;
        $this->url = $url;
        $this->hydrator = $hydrator;
        $this->clientCredentials = $clientCredentials;
        $this->organizationStorage = $organizationStorage;
    }

    /**
     * @param EventInterface $event
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(EventInterface $event) {
        /** @var PreProcess $preprocess */
        $preprocess = $event->getTarget();

        if (isset($preprocess->getData()['organization'])) {

            $entity = $this->getOrganizationFromUserPost($preprocess->getData()['organization']);

            if (!$entity) {

                $response = $this->getRequest($preprocess);
                $entity = new OrganizationEntity();

                $this->hydrator->hydrate(
                    json_decode($response->getBody()->getContents(), true),
                    $entity
                );
            }

            $preprocess->getEntity()->appendOrganization(new Reference(
                $entity->getId(),
                'organization'
            ));

        }
    }

    /**
     * @param PreProcess $preProcess
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getRequest(PreProcess $preProcess) {

        $tokenResponse = json_decode($this->getToken()->getBody()->getContents(), true);
       
        $data = [
            // 'debug' => true,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $tokenResponse['access_token']
            ],
            'json' => [
                'name' => $preProcess->getData()['organization'],
            ]

        ];

        return $this->client->post(
            $this->url . '/organization',
            $data
        );
    }

    protected function getToken() {

        $data = [
            // 'debug' => true,
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => $this->clientCredentials
        ];

        return $this->client->post(
            $this->url . '/access-token',
            $data
        );
    }

    /**
     * Undocumented function
     *
     * @param [string] $orgData
     * @return void
     */
    protected function getOrganizationFromUserPost($orgData) {
        $entity = null;

        try {
            $mongoId = new ObjectId($orgData);
            $entity = $this->organizationStorage->get($orgData);
            
        } catch (Exception $e) {

            // TODO LOG???
        }

        return $entity;
    }
}; 