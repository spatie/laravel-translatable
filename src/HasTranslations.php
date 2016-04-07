<?php

namespace Spatie\Translatable;

trait HasTranslations
{
    public function translate(string $fieldName, string $locale = '') : string
    {
        return $this->getTranslation($fieldName, $locale);
    }

    public function getTranslation(string $fieldName, string $locale) : string
    {

    }

    public function setTranslation(string $fieldName, string $locale, string $value)
    {

    }

    public function setTranslations(string $fieldName, array $values)
    {
        foreach($values as $locale => $value) {
            $this->setTranslation($fieldName, $locale, $value);
        }
    }
}
