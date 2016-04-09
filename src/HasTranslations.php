<?php

namespace Spatie\Translatable;

use Spatie\Translatable\Events\TranslationHasBeenSet;
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

    /**
     * @param string $attributeName
     * @param string $locale
     *
     * @return mixed
     */
    public function translate(string $attributeName, string $locale = '')
    {
        return $this->getTranslation($attributeName, $locale);
    }

    /***
     * @param string $attributeName
     * @param string $locale
     * @return mixed
     */
    public function getTranslation(string $attributeName, string $locale)
    {
        $translations = $this->getTranslations($attributeName);

        return $translations[$locale] ?? $this->castTranslation('', $attributeName);
    }

    public function getTranslations(string $attributeName) : array
    {
        $this->guardAgainstUntranslatableAttribute($attributeName);

        $translations = json_decode($this->getAttributes()[$attributeName] ?? '{}', true);

        $castTranslations = array_map(function ($translation) use ($attributeName) {
            return $this->castTranslation($translation, $attributeName);
        }, $translations);

        return $castTranslations;
    }

    public function setTranslation(string $attributeName, string $locale, $value)
    {
        $this->guardAgainstUntranslatableAttribute($attributeName);

        $translations = $this->getTranslations($attributeName);

        $oldValue = $translations[$locale] ?? '';

        $translations[$locale] = $value;

        $this->setAttribute($attributeName, $translations);

        event(new TranslationHasBeenSet($this, $attributeName, $locale, $oldValue, $value));

        return $this;
    }

    public function setTranslations(string $attributeName, array $translations)
    {
        $this->guardAgainstUntranslatableAttribute($attributeName);

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
        return TranslatableAttributeCollection::createForModel($this)->isTranslatable($attributeName);
    }

    protected function castTranslation($translation, $attributeName)
    {
        $cast = TranslatableAttributeCollection::createForModel($this)->getCast($attributeName);

        if ($cast === 'bool') {
            return boolval($translation);
        }

        if ($cast === 'integer') {
            return intval($translation);
        }

        if ($cast === 'float') {
            return floatval($translation);
        }

        if ($cast === 'array') {
            return (array) $translation;
        }

        return $translation;
    }

    protected function guardAgainstUntranslatableAttribute(string $attributeName)
    {
        if (!$this->isTranslatableAttribute($attributeName)) {
            throw Untranslatable::attributeIsNotTranslatable($attributeName, $this);
        }
    }
}
