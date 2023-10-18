<?php
declare(strict_types=1);

namespace App\Module\Organization\Entity;

use App\Module\Organization\Entity\Embedded\Address\Address;
use App\Module\Organization\Entity\Embedded\Phone\Phone;
use App\Storage\Entity\EntityInterface;
use App\Storage\Entity\EntityTrait;
use App\Storage\Entity\Reference;
use App\Storage\Entity\ReferenceInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * Class OrganizationEntity
 * @package App\Module\Organization\Entity
 */
class OrganizationEntity implements EntityInterface, UserEntityInterface {

    use EntityTrait;

    /**
     * OrganizationEntity constructor.
     */
    public function __construct() {
        
        $this->logo = new Reference();
    }

    /**
     */
    public function getIdentifier() {
        return 'organization_' . $this->getId();
    }

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var ReferenceInterface
     */
    protected $logo;

    /**
     *
     * @var string
     */
    protected $oauthToken;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return OrganizationEntity
     */
    public function setName(string $name): OrganizationEntity {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return ReferenceInterface
     */
    public function getLogo(): ReferenceInterface {
        return $this->logo;
    }

    /**
     * @param ReferenceInterface $logo
     * @return OrganizationEntity
     */
    public function setLogo(ReferenceInterface $logo): OrganizationEntity {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get the value of oauthToken
     *
     * @return  string
     */ 
    public function getOauthToken()
    {
        return $this->oauthToken;
    }

    /**
     * Set the value of oauthToken
     *
     * @param  string  $oauthToken
     *
     * @return  self
     */ 
    public function setOauthToken(string $oauthToken) {
        $this->oauthToken = $oauthToken;
        return $this;
    }
}