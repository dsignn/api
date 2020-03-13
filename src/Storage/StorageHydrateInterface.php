<?php
declare(strict_types=1);

namespace App\Storage;

use Laminas\Hydrator\HydratorAwareInterface;

/**
 * Interface StorageInterface
 * @package App\Storage
 */
interface StorageHydrateInterface extends StorageInterface, HydratorAwareInterface, ObjectPrototypeInterface { }