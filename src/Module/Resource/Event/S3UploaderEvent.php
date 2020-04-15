<?php
declare(strict_types=1);

namespace App\Module\Resource\Event;

use Aws\Result;
use Aws\S3\S3Client;
use Laminas\EventManager\EventInterface;

/**
 * Class S3UploaderEvent
 * @package App\Module\Resource\Event
 */
class S3UploaderEvent {

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

        /** @var Result $result */
        $result = $this->s3Client->putObject(
            array(
                'Bucket'=> $this->bucketName,
                'Key' =>  $this->uuid(),
                'SourceFile' => $event->getTarget()->getSrc(),
                'ACL'    => 'public-read'
            )
        );

        $event->getTarget()->setSrc($result->get('@metadata')['effectiveUri']);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function uuid(){
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}