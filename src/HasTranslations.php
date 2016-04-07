<?php

namespace Spatie\Translatable;

use Spatie\Translatable\Exceptions\Untranslatable;

trait HasTranslations
{
    /**
     * @param string $fieldName
     *
     * @return mixed
     */
    public function getAttributeValue($fieldName)
    {
        if (!$this->isTranslatableField($fieldName)) {
            return parent::getAttributeValue($fieldName);
        }

        return $this->getTranslation($fieldName, app()->getLocale());
    }

    public function translate(string $fieldName, string $locale = '', string $default = '') : string
    {
        return $this->getTranslation($fieldName, $locale, $default);
    }

    public function getTranslation(string $fieldName, string $locale, string $default = '') : string
    {
        $translations = $this->getTranslations($fieldName);

        return $translations[$locale] ?? $default;
    }

    public function getTranslations(string $fieldName) : array
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        $translations = json_decode($this->getAttributes()[$fieldName], true);

        return $translations;
    }

    public function getTranslatedLocales(string $fieldName) : array
    {
        return array_keys($this->getTranslations($fieldName));
    }

    public function setTranslation(string $fieldName, string $locale, string $value)
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        $currentValue = json_decode($this->getAttributes()[$fieldName] ?? '{}', true);

        $currentValue[$locale] = $value;

        $this->setAttribute($fieldName, $currentValue);

        return $this;
    }

    public function setTranslations(string $fieldName, array $values)
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        foreach ($values as $locale => $value) {
            $this->setTranslation($fieldName, $locale, $value);
        }
    }

    protected function isTranslatableField(string $fieldName) : bool
    {
        return in_array($fieldName, $this->getTranslatableFields());
    }

    protected function guardAgainstUntranslatableFieldName(string $fieldName)
    {
        if (!$this->isTranslatableField($fieldName)) {
            throw Untranslatable::fieldIsNotTranslatable($fieldName, $this);
        }
    }
}
