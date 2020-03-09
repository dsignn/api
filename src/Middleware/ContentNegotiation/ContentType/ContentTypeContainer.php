<?php
declare(strict_types=1);

namespace App\Middleware\ContentNegotiation\ContentType;

use DI\Container;

class ContentTypeContainer extends Container {

    /**
     * @param string $name
     * @param \DI\Definition\Helper\DefinitionHelper|mixed $value
     * @throws \Exception
     */
    public function set(string $name, $value) {

        if (!($value instanceof ContentTypeTransformInterface)) {
            $type = gettype($value) === 'object' ? get_class($value) :  gettype($value);
            throw new \Exception("Wrong interface " . $type);
        }

        parent::set($name, $value);
    }
}