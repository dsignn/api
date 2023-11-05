<?php
declare(strict_types=1);

namespace App\Module\Resource\Http\QueryString;

use App\Middleware\QueryString\QueryStringInterface;
use Exception;
use MongoDB\BSON\ObjectId;

/**
 * Class ResourceQueryString
 * @package App\Module\Monitor\Http\QueryString
 */
class ResourceQueryString implements QueryStringInterface {

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
                        $query['organization_reference.id'] = new ObjectId($value);
                    } catch (Exception $e) {
                        // TODO 
                    }
                    break;
            }
        }

        return $query;
    }
}