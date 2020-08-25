<?php
declare(strict_types=1);

namespace App\Validator\File;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;

/**
 * Class FileMimeType
 * @package App\Validator\File
 */
class FileMimeType extends AbstractValidator {

    const TOO_BIG   = 'fileMimeTypeInvalid';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = [
        self::TOO_BIG   => "Invalid mime type '%mimeType%'"
    ];

    /**
     * @var array Error message template variables
     */
    protected $messageVariables = [
        'mimeType' => 'mimeType',
    ];

    /**
     * @var array
     */
    protected $mimeTypes = [];

    /**
     * @var string
     */
    protected $mimeType = '';

    /**
     * FileSize constructor.
     * @param null $options
     */
    public function __construct($options = null) {
        parent::__construct($options);
        if (is_array($options) && isset($options['mimeTypes'])) {
            $this->mimeTypes = $options['mimeTypes'];
        }
    }

    /**
     * @inheritDoc
     */
    public function isValid($value) {

        $isValid = true;
        $this->setMimeType(isset($value['mimeType']) ? $value['mimeType'] : '');
        if (!in_array($this->mimeType, $this->mimeTypes)) {
            $this->error(self::TOO_BIG);
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * @param string $mimeType
     * @return FileMimeType
     */
    public function setMimeType(string $mimeType) {
        $this->mimeType = $mimeType;
        return $this;
    }
}