<?php
declare(strict_types=1);

namespace App\Module\Resource\Entity;

use App\Module\Organization\Entity\OrganizationAwareInterface;
use App\Module\Organization\Entity\OrganizationAwareTrait;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;

/**
 * Class AbstractResourceEntity
 * @package App\Module\Resource\Entity
 */
abstract class AbstractResourceEntity implements EntityInterface, OrganizationAwareInterface {

    use EntityTrait, OrganizationAwareTrait;

    /**
     * @var string
     */
    protected $mimeType = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var int
     */
    protected $size = 0;

    /**
     * @var string
     */
    protected $src = '';

    /**
     * @var string
     */
    protected $s3path = '';

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return AbstractResourceEntity
     */
    public function setMimeType(string $mimeType): AbstractResourceEntity {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AbstractResourceEntity
     */
    public function setName(string $name): AbstractResourceEntity {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return AbstractResourceEntity
     */
    public function setSize(int $size): AbstractResourceEntity {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getSrc(): string
    {
        return $this->src;
    }

    /**
     * @param string $src
     * @return AbstractResourceEntity
     */
    public function setSrc(string $src): AbstractResourceEntity {
        $this->src = $src;
        return $this;
    }

    /**
     * @return string
     */
    public function getS3path(): string {
        return $this->s3path;
    }

    /**
     * @param string $S3path
     * @return AbstractResourceEntity
     */
    public function setS3path(string $S3path): AbstractResourceEntity {
        $this->s3path = $S3path;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return AbstractResourceEntity
     */
    public function setTags(array $tags): AbstractResourceEntity {
        $this->tags = $tags;
        return $this;
    }


}