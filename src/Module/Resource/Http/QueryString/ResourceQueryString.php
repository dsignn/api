<?php
declare(strict_types=1);

namespace App\Module\Resource\Http\QueryString;

use App\Middleware\QueryString\QueryStringInterface;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;

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

            switch (true) {
                case $key === 'name':
                    $query[$key] = new Regex(preg_quote($value), 'i');
                    break;
                case $key === 'organization_reference':
                    try {
                        $query['organization_reference.id'] = new ObjectId($value);
                    } catch (Exception $e) {
                        // TODO 
                    }
                    break;
                case $key === 'size':
                    if (is_array($data[$key]) && isset($data[$key]['direction']) && isset($data[$key]['value'])) {
                        $mb = (int)$data[$key]['value'] * 1000000;
                        $query['size'] = [$data[$key]['direction'] == 'down' ? '$lte' : '$gte' => $mb];
                    }
                    break;
                case $key === 'height':
                    if (is_array($data[$key]) && isset($data[$key]['direction']) && isset($data[$key]['value'])) {
                        $query['dimension.height'] = [$data[$key]['direction'] == 'down' ? '$lte' : '$gte' => (int) $data[$key]['value']];
                    }
                    break;
                case $key === 'width':
                    if (is_array($data[$key]) && isset($data[$key]['direction']) && isset($data[$key]['value'])) {
                        $query['dimension.width'] = [$data[$key]['direction'] == 'down' ? '$lte' : '$gte' => (int) $data[$key]['value']];
                    }
                    break;
                case $key === 'tags':
                    $query[$key] = ['$in' => $value];
                    break;
            }
        }

        return $query;
    }
}