<?php
declare(strict_types=1);

namespace App\Module\Monitor\Controller;

use App\Controller\RestController;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class MonitorController
 * @package App\Module\Monitor\Controller
 */
class MonitorController extends RestController
{
    /**
     * @var string
     */
    protected $entityNameClass = 'MonitorEntity';

    /**
     * MonitorController constructor.
     * @param MonitorStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MonitorStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}