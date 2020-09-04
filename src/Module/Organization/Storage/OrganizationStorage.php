<?php
declare(strict_types=1);

namespace App\Module\Organization\Storage;

use App\Storage\Adapter\Mongo\MongoAdapter;
use App\Storage\Storage;
use MongoDB\Driver\Cursor;

/**
 * Class OrganizationStorage
 * @package App\Module\Organization\Storage
 */
class OrganizationStorage extends Storage implements OrganizationStorageInterface {

}