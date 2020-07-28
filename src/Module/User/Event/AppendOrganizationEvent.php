<?php
declare(strict_types=1);

namespace App\Module\User\Event;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Storage\Entity\Reference;
use App\Storage\Event\PreProcess;
use GuzzleHttp\Client;
use Laminas\EventManager\EventInterface;
use Laminas\Hydrator\HydrationInterface;

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
     * @var Client
     */
    protected $client;

    /**
     * @var HydrationInterface
     */
    protected $hydrator;

    /**
     * AppendOrganizationEvent constructor.
     * @param Client $client
     * @param string $url
     * @param HydrationInterface $hydrator
     */
    public function __construct(Client $client, string $url, HydrationInterface $hydrator) {
        $this->client = $client;
        $this->url = $url;
        $this->hydrator = $hydrator;
    }

    /**
     * @param EventInterface $event
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(EventInterface $event) {
        /** @var PreProcess $preprocess */
        $preprocess = $event->getTarget();

        if (isset($preprocess->getData()['nameOrganization'])) {

            $response = $this->getRequest($preprocess);
            $organization = new OrganizationEntity();

            $this->hydrator->hydrate(
                json_decode($response->getBody()->getContents(), true),
                $organization
            );

            $preprocess->getEntity()->appendOrganization(new Reference(
                $organization->getId(),
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
        $data = [
           // 'debug' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'name' => $preProcess->getData()['nameOrganization'],
            ]

        ];

        return $this->client->post(
            $this->url . '/organization',
            $data
        );
    }
};