<?php
declare(strict_types=1);

namespace App\Module\Playlist\Http\QueryString;

use App\Middleware\QueryString\QueryStringInterface;
use Exception;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;

/**
 * Class PlaylistQueryString
 * @package App\Module\Monitor\Http\QueryString
 */
class PlaylistQueryString implements QueryStringInterface {

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
                case $key === 'tags':
                    $query[$key] = ['$in' => $value];
                    break;
            }
        }

        return $query;
    }
}