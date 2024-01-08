<?php
declare(strict_types=1);

namespace App\Module\Playlist\Controller;

use App\Controller\AllRpcController;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Module\Playlist\Storage\PlaylistStorageInterface;
use App\Module\Resource\Storage\ResourceStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AllRpcPlaylistController
 * @package App\Module\Monitor\Controller
 */
class AllRpcPlaylistController extends AllRpcController {

    /**
     * AllRpcResourceController constructor.
     * @param PlaylistStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(PlaylistStorageInterface $storage, ContainerInterface $container) {
       parent::__construct($storage, $container);
    }
}