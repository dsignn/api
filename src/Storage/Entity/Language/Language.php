<?php
declare(strict_types=1);

namespace App\Storage\Entity\Language;

/**
 * Class Language
 * @package App\Storage\Entity\Language
 */
class Language {

    /**
     * @var
     */
    protected $language;

    /**
     * @var
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getLanguage() {
        return $this->language;
    }

    /**ù
     * @param $language
     * @return Language
     */
    public function setLanguage($language): Language {
        $this->language = $language;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param $value
     * @return Language
     */
    public function setValue($value): Language {
        $this->value = $value;
        return $this;
    }
}