<?php

namespace Spatie\Translatable;

use Spatie\Translatable\Exceptions\Untranslatable;

trait HasTranslations
{
    /**
     * @param string $attributeName
     *
     * @return mixed
     */
    public function getAttributeValue($attributeName)
    {
        if (!$this->isTranslatableAttribute($attributeName)) {
            return parent::getAttributeValue($attributeName);
        }

        return $this->getTranslation($attributeName, config('app.locale'));
    }

    public function translate(string $attributeName, string $locale = '', string $default = '') : string
    {
        return $this->getTranslation($attributeName, $locale, $default);
    }

    public function getTranslation(string $attributeName, string $locale, string $default = '') : string
    {
        $translations = $this->getTranslations($attributeName);

        return $translations[$locale] ?? $default;
    }

    public function getTranslations(string $attributeName) : array
    {
        $this->guardAgainstUntranslatableFieldName($attributeName);

        $translations = json_decode($this->getAttributes()[$attributeName] ?? '{}', true);

        return $translations;
    }

    public function setTranslation(string $attributeName, string $locale, string $value)
    {
        $this->guardAgainstUntranslatableFieldName($attributeName);

        $translations = $this->getTranslations($attributeName);

        $translations[$locale] = $value;

        $this->setAttribute($attributeName, $translations);

        return $this;
    }

    public function setTranslations(string $attributeName, array $translations)
    {
        $this->guardAgainstUntranslatableFieldName($attributeName);

        foreach ($translations as $locale => $translation) {
            $this->setTranslation($attributeName, $locale, $translation);
        }

        return $this;
    }

    public function forgetTranslation(string $attributeName, string $locale)
    {
        $translations = $this->getTranslations($attributeName);

        unset($translations[$locale]);

        $this->setAttribute($attributeName, $translations);

        return $this;
    }

    public function getTranslatedLocales(string $attributeName) : array
    {
        return array_keys($this->getTranslations($attributeName));
    }

    protected function isTranslatableAttribute(string $attributeName) : bool
    {
        return in_array($attributeName, $this->getTranslatableAttributes());
    }

    protected function guardAgainstUntranslatableFieldName(string $attributeName)
    {
        if (!$this->isTranslatableAttribute($attributeName)) {
            throw Untranslatable::attributeIsNotTranslatable($attributeName, $this);
        }
    }
}
