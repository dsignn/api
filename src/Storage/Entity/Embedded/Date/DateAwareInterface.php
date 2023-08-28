<?php
declare(strict_types=1);

namespace App\Storage\Entity\Embedded\Date;

use DateTime;

/**
 * interface DateAwareInterface
 */
interface DateAwareInterface {

     /**
     * @return float
     */
    public function getCreatedDate(): DateTime;

    /**
     * @param float $value
     * @return self
     */
    public function setCreatedDate(DateTime $date);

    /**
     * @return float
     */
    public function getLastUpdateDate(): DateTime;

    /**
     * @param float $value
     * @return self
     */
    public function setLastUpdateDate(DateTime $date);

}