<?php
declare(strict_types=1);

namespace App\Storage\Event;

use DateTime;
use Laminas\EventManager\EventInterface;

/**
 * Class AttachDateTimeCallback
 * @package App\Storage\Event
 */
class AttachDateTimeCallback {

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $nameProperty = '';

    /**
     * Undocumented function
     * @param string $property
     */
    public function __construct(string $property) {
        $this->nameProperty = $property;
    } 

        /**
     * @param EventInterface $event
     * @throws \Exception
     */
    public function __invoke(EventInterface $event) {
        
        $event->getTarget()->{'set'. ucfirst($this->nameProperty)}(new DateTime());
    }
}