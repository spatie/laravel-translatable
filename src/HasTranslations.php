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

        $translations = json_decode($this->getAttributes()[$fieldName] ?? '{}', true);

        return $translations;
    }

    public function setTranslation(string $fieldName, string $locale, string $value)
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        $translations = $this->getTranslations($fieldName);

        $translations[$locale] = $value;

        $this->setAttribute($fieldName, $translations);

        return $this;
    }

    public function setTranslations(string $fieldName, array $translations)
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        foreach ($translations as $locale => $translation) {
            $this->setTranslation($fieldName, $locale, $translation);
        }

        return $this;
    }

    public function forgetTranslation(string $fieldName, string $locale)
    {
        $translations = $this->getTranslations($fieldName);

        unset($translations[$locale]);

        $this->setAttribute($fieldName, $translations);

        return $this;
    }

    public function getTranslatedLocales(string $fieldName) : array
    {
        return array_keys($this->getTranslations($fieldName));
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
