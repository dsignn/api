<?php
declare(strict_types=1);

namespace App\Module\Resource\Filter;

use Laminas\Filter\Exception;
use Laminas\Filter\FilterInterface;
use Slim\Psr7\UploadedFile;

/**
 * Class FileUnpacking
 * @package App\Module\Resource\Filter
 */
class FileUnpacking implements FilterInterface {

    /**
     * @param mixed $value
     * @return mixed|void
     */
    public function filter($value) {

        switch (true) {
            case $value instanceof  UploadedFile === true:
                $data['size'] = $value->getSize();
                $data['mimeType'] = $value->getClientMediaType();
                $data['src'] = $value->getStream()->getMetadata('uri');
                $value = $data;
                break;
            case is_array($value) === true:
                $data['size'] = $value['size'];
                $data['mimeType'] = $value['type'];
                $data['src'] = $value['tmp_name'];
                $value = $data;
                break;
        }

        return $value;
    }
}