<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\Accept;

use DI\Container;
use Exception;

/**
 * Class AcceptContainer
 * @package App\Middleware\ContentNegotiation\Accept
 */
class AcceptContainer extends Container {

    public function set(string $name, $value) {

        if (!($value instanceof AcceptTransformInterface)) {
            $type = gettype($value) === 'object' ? get_class($value) :  gettype($value);
            throw new Exception("Wrong interface " . $type);
        }

        parent::set($name, $value);
    }
}