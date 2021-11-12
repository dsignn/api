<?php
declare(strict_types=1);

namespace App\Module\Order\Entity\Embedded;

use App\Module\Restaurant\Entity\Embedded\MenuItem;

/**
 * Class MenuOrder
 * @package App\Module\Order\Entity\Embedded
 */
class MenuOrder extends MenuItem {

   const TYPE_MENU = 'menu-order';

   /**
    * @var [string]
    */
   protected $type= self::TYPE_MENU;

   /**
    * @return string
    */
   public function getType() {
       return $this->type;
   }
}