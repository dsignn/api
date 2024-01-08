<?php
declare(strict_types=1);

namespace App\Filter\File;

use Laminas\Filter\FilterInterface;
use Slim\Psr7\UploadedFile;

/**
 * Class FileTransform
 * @package App\Module\Resource\Filter
 */
class FileTransform implements FilterInterface {

    /**
     * @param mixed $value
     * @return mixed|void
     */
    public function filter($value) {

        switch (true) {
            case $value instanceof  UploadedFile === true:
                $data['src'] = $value->getStream()->getMetadata('uri');
                $data['mimeType'] = mime_content_type($data['src']);
                $data['size'] = filesize($data['src']);
                $value = $data;
                break;
            case is_array($value) === true:

                $data['src'] = $value['tmp_name'];
                $data['mimeType'] = mime_content_type($data['src']);
                $data['size'] = filesize($data['src']);
                $value = $data;
                break;
        }

        return $value;
    }
}