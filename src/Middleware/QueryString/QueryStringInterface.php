<?php
declare(strict_types=1);

namespace App\Middleware\QueryString;

/**
 * Class QueryStringInterface
 * @package App\Middleware\QueryString;
 */
interface QueryStringInterface {

    /**
     * @param array $data
     * @return void
     */
    public function computeQueryString(array $data);
}
