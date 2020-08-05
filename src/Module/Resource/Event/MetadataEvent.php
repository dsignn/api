<?php
declare(strict_types=1);

namespace App\Module\Resource\Event;

use App\Module\Resource\Entity\AbstractResourceEntity;
use App\Module\Resource\Entity\Embedded\Dimension;
use App\Module\Resource\Entity\ImageResourceEntity;
use App\Module\Resource\Entity\VideoResourceEntity;
use FFMpeg\FFProbe;
use Laminas\EventManager\EventInterface;

/**
 * Class MetadataEvent
 * @package App\Module\Resource\Event
 */
class MetadataEvent {

    /**
     * @var array
     */
    protected $binary = [];

    /**
     * MetadataEvent constructor.
     * @param array $binary
     */
    public function __construct(array $binary) {
        $this->binary = $binary;
    }

    /**
     * @param EventInterface $event
     * @throws \Exception
     */
    public function __invoke(EventInterface $event) {

        /** @var AbstractResourceEntity $entity */
        $entity = $event->getTarget();
        if ($this->skipProbe($entity)) {
            return;
        }

        $ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries'  => '/usr/bin/ffprobe',
        ]);

        switch (true) {
            case $entity instanceof ImageResourceEntity === true:
                /** @var ImageResourceEntity $entity */
                $stream = $ffprobe->streams($entity->getSrc())->videos()->first();
                $entity->setDimension(new Dimension($stream->get('width'), $stream->get('height')));
                break;
            case $entity instanceof VideoResourceEntity === true:
                /** @var VideoResourceEntity $entity */
                $stream = $ffprobe->streams($entity->getSrc())->videos()->first();
                $entity->setDimension(new Dimension($stream->get('width'), $stream->get('height')));
                $entity->setDuration((float) $stream->get('duration'));
                $entity->setAspectRatio($stream->get('sample_aspect_ratio'));
                break;
        }
    }

    /**
     * @param AbstractResourceEntity $entity
     * @return bool
     */
    protected function skipProbe(AbstractResourceEntity $entity) {
        return strpos($entity->getSrc(), "http") === false ? false : true;
    }
}