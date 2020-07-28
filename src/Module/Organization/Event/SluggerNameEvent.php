<?php
declare(strict_types=1);

namespace App\Module\Organization\Event;

use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Url\SlugifyInterface;
use Laminas\EventManager\EventInterface;

/**
 * Class NormalizeNameEvent
 * @package App\Module\Organization\Event
 */
class SluggerNameEvent {

    /**
     * @var SlugifyInterface
     */
    protected $slugger;

    /**
     * NormalizeNameEvent constructor.
     * @param SlugifyInterface $slugger
     */
    public function __construct(SlugifyInterface $slugger) {
        $this->slugger = $slugger;
    }

    /**
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event) {

        /** @var OrganizationEntity $entity */
        $entity = $event->getTarget();

        if ($entity instanceof OrganizationEntity) {
            // TODO configurable
            $entity->setNormalizeName($this->slugger->slugify($entity->getName()));
        }
    }
}