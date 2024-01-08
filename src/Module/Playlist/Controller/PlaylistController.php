<?php
declare(strict_types=1);

namespace App\Module\Playlist\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Playlist\Storage\PlaylistStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PlaylistController
 * @package App\Module\Monitor\Controller
 */
class PlaylistController extends RestController implements RestControllerInterface {

    /**
     * MonitorController constructor.
     * @param PlaylistStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(PlaylistStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}