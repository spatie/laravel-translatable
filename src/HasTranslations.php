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
    public function getAttribute(string $fieldName)
    {
        if (!$this->isTranslatableField($fieldName)) {
            return parent::getAttribute($fieldName);
        }

        return $this->getTranslation($fieldName, app()->getLocale());
    }

    public function translate(string $fieldName, string $locale = '', string $default = '') : string
    {
        return $this->getTranslation($fieldName, $locale, $default);
    }

    public function getTranslation(string $fieldName, string $locale, string $default = '') : string
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        return $this->$fieldName[$locale] ?? $default;
    }

    public function setTranslation(string $fieldName, string $locale, string $value)
    {
        $this->guardAgainstUntranslatableFieldName($fieldName);

        $this->$fieldName[$locale] = $value;

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
