<?php
declare(strict_types=1);

namespace App\Module\Resource\Event;

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

        $ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries'  => '/usr/bin/ffprobe',
        ]);

        switch (true) {
            case $event->getTarget() instanceof ImageResourceEntity === true:
                /** @var FFProbe\DataMapping\Stream $stream */
                $stream = $ffprobe->streams($event->getTarget()->getSrc())->videos()->first();
                $event->getTarget()->setDimension(new Dimension($stream->get('width'), $stream->get('height')));
                break;
            case $event->getTarget() instanceof VideoResourceEntity === true:
                $stream = $ffprobe->streams($event->getTarget()->getSrc())->videos()->first();
                $event->getTarget()->setDimension(new Dimension($stream->get('width'), $stream->get('height')));
                $event->getTarget()->setDuration((float) $stream->get('duration'));
                $event->getTarget()->setAspectRatio($stream->get('sample_aspect_ratio'));
                break;
        }
    }
}