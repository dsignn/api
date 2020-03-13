<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use App\Storage\Entity\EntityInterface;
use App\Storage\ResultSet\ResultSetPaginateInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Stream;
use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorAwareTrait;


/**
 * Class JsonContentType
 * @package App\Middleware\ContentNegotiation\ContentType
 */
class JsonContentType implements ContentTypeTransformInterface {

    use HydratorAwareTrait;

    public function transformContentType(Response $response, $data): Response {

        $computeData = [];

        switch (true) {
            case $data instanceof ResultSetPaginateInterface === true:
                /** @var ResultSetPaginateInterface $data */

                if ($data instanceof HydratorAwareInterface && $this->getHydrator()) {
                    $data->setHydrator($this->getHydrator());
                }

                $computeData['meta'] = [
                    'page' => $data->getPage(),
                    'item-per-page' => $data->getItemPerPage(),
                    'total-count' => $data->count()
                ];

                $computeData['data'] = $data->toArray();
                break;
            case $data instanceof EntityInterface === true:
                $computeData = $this->getHydrator()->extract($data);
                true;
        }

        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($json = json_encode($computeData));

        if ($json === false) {
            return $response->withStatus(415);
        }

        return $response->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }
}