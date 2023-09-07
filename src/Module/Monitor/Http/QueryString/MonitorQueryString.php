<?php
declare(strict_types=1);

namespace App\Module\Monitor\Http\QueryString;

use App\Middleware\QueryString\QueryStringInterface;
use Exception;
use MongoDB\BSON\ObjectId;
use phpDocumentor\Reflection\Types\Object_;

/**
 * Class MonitorQueryString
 * @package App\Module\Monitor\Http\QueryString
 */
class MonitorQueryString implements QueryStringInterface {

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