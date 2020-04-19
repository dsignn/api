<?php
declare(strict_types=1);

namespace App\Module\Timeslot\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Timeslot\Storage\TimeslotStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class TimeslotController
 * @package App\Module\Timeslot\Controller
 */
class TimeslotController extends RestController implements RestControllerInterface  {

    /**
     * @var string
     */
    protected $hydratorService = 'RestTimeslotEntityHydrator';

    /**
     * @inheritDoc
     */
    public function __construct(TimeslotStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }

}