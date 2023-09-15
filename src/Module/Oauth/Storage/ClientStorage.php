<?php
declare(strict_types=1);

namespace App\Module\Oauth\Storage;

use App\Storage\Entity\EntityInterface;
use App\Storage\Storage;
/**
 * Interface ClientStorage
 * @package App\Module\Oauth\Storage
 */
class ClientStorage extends Storage implements ClientStorageInterface { 

        /**
     * @inheritDoc
     */
    public function save(EntityInterface &$entity): EntityInterface {

        $this->events->trigger(
            Storage::$BEFORE_SAVE,
            $entity
        );

        $dataSave = $this->storage->save($this->hydrator->extract($entity));
        $this->hydrator->hydrate($dataSave, $entity);

        $this->events->trigger(
            Storage::$AFTER_SAVE,
            $entity
        );
        
        return $entity;
    }
}