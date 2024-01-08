<?php
declare(strict_types=1);

namespace App\Validator\File;

use Laminas\Validator\File\Size;

/**
 * Class FileSize
 * @package App\Validator\File
 */
class FileSize extends Size {

    const TOO_BIG   = 'fileSizeTooBig';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = [
        self::TOO_BIG   => "Maximum allowed size for file is '%max%' but '%size%' detected"
    ];

    /**
     * @var array Error message template variables
     */
    protected $messageVariables = [
        'max'  => ['options' => 'max'],
        'size' => 'size',
    ];


    /**
     * @var int
     */
    protected $max = 0;

    /**
     * @var int
     */
    protected $size = 0;

    /**
     * FileSize constructor.
     * @param null $options
     */
    public function __construct($options = null) {
        parent::__construct($options);
        if (is_array($options) && isset($options['max'])) {
            $this->max = $this->fromByteString($options['max']);
        }
    }

    /**
     * @inheritDoc
     */
    public function isValid($value, $file = null) {

        $isValid = true;
        $this->setSize(isset($value['size']) ? $value['size'] : 0);
        if ($this->size > 0 && $this->max < $this->size) {
            $this->error(self::TOO_BIG);
            $isValid = false;
        }
        return $isValid;
    }
}