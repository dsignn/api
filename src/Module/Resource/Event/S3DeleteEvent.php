<?php
declare(strict_types=1);

namespace App\Module\Resource\Event;

use App\Module\Resource\Entity\AbstractResourceEntity;
use Aws\Exception\AwsException;
use Aws\Result;
use Aws\S3\S3Client;
use Laminas\EventManager\EventInterface;

/**
 * Class S3DeleteEvent
 * @package App\Module\Resource\Event
 */
class S3DeleteEvent {

    /**
     * @var string
     */
    protected $bucketName = '';

    protected $s3Client;

    /**
     * S3UploaderEvent constructor.
     * @param S3Client $s3Client
     * @param string $bucketName
     */
    public function __construct(S3Client $s3Client, string $bucketName) {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
    }

    /**
     * @param EventInterface $event
     * @throws \Exception
     */
    public function __invoke(EventInterface $event) {

        /** @var AbstractResourceEntity $entity */
        $entity = $event->getTarget();

        /** @var Result $result */
        $result = $this->s3Client->deleteObject(
            array(
                'Bucket'=> $this->bucketName,
                'Key' =>  $entity->getS3path()
            )
        );
    }
}