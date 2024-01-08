<?php
declare(strict_types=1);

namespace App\Storage\Entity\Embedded\Date;

use DateTime;

/**
 * trait DateAwareInterfaceTrait
 */
trait DateAwareInterfaceTrait {

    /**
     *
     * @var [DateTime]
     */
    public $createdDate;

    /**
     *
     * @var [DateTime]
     */
    public $lastUpdateDate;

    /**
     * @return DateTime
     */
    public function getCreatedDate(): DateTime {
        return $this->createdDate;
    }

    /**
     * @param DateTime $value
     * @return self
     */
    public function setCreatedDate(DateTime $date): self {
        $this->createdDate = $date;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastUpdateDate(): DateTime {
        return $this->lastUpdateDate;
    }

    /**
     * @param DateTime $value
     * @return self
     */
    public function setLastUpdateDate(DateTime $date): self {
        $this->lastUpdateDate = $date;
        return $this;
    }
}