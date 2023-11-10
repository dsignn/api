<?php
declare(strict_types=1);

namespace App\Module\User\Http\QueryString;

use App\Middleware\QueryString\QueryStringInterface;
use Exception;
use MongoDB\BSON\ObjectId;

/**
 * Class ResourceQueryString
 * @package App\Module\User\Http\QueryString
 */
class UserQueryString implements QueryStringInterface {

    /**
     * @param array $data
     * @return void
     */
    public function computeQueryString(array $data) {

        $query = [];

        foreach($data as $key => $value) {
          
            switch(true) {
                case $key === 'organization_reference':
                    try {
                        $query['organizations.id'] = new ObjectId($value);
                    } catch (Exception $e) {
                        // TODO 
                    }
                    break;
            }
        }

        return $query;
    }
}