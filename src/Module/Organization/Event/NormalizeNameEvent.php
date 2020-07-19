<?php
declare(strict_types=1);

namespace App\Module\Organization\Event;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Util\UrlNormalizer;
use Laminas\EventManager\EventInterface;

/**
 * Class NormalizeNameEvent
 * @package App\Module\Organization\Event
 */
class NormalizeNameEvent {

    /**
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event) {

        /** @var OrganizationEntity $entity */
        $entity = $event->getTarget();

        if ($entity instanceof OrganizationEntity) {
            // TODO configurable
            $entity->setNormalizeName(UrlNormalizer::normalize($entity->getName()));
        }
    }
}