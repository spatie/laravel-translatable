<?php

namespace Spatie\Translatable;

use Illuminate\Support\Str;
use Spatie\Translatable\Events\TranslationHasBeenSet;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;

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
     *
     * @return mixed
     */
    public function getTranslation(string $attributeName, string $locale)
    {
        $translations = $this->getTranslations($attributeName);

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($attributeName)) {
            return $this->mutateAttribute($attributeName, $translation);
        }

        return $translation;
    }

    public function getTranslations($attributeName) : array
    {
        $this->guardAgainstUntranslatableAttribute($attributeName);

        return json_decode($this->getAttributes()[$attributeName] ?? '{}', true);
    }

    public function setTranslation(string $attributeName, string $locale, $value)
    {
        $this->guardAgainstUntranslatableAttribute($attributeName);

        $translations = $this->getTranslations($attributeName);

        $oldValue = $translations[$locale] ?? '';

        if ($this->hasSetMutator($attributeName)) {
            $method = 'set' . Str::studly($attributeName) . 'Attribute';
            $value = $this->{$method}($value);
        }

        $translations[$locale] = $value;

        $this->attributes[$attributeName] = $this->asJson($translations);

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
        return in_array($attributeName, $this->getTranslatableAttributes());
    }

    protected function guardAgainstUntranslatableAttribute(string $attributeName)
    {
        if (!$this->isTranslatableAttribute($attributeName)) {
            throw AttributeIsNotTranslatable::make($attributeName, $this);
        }
    }

    public function getCasts()
    {
        return array_merge(
            parent::getCasts(),
            array_fill_keys($this->getTranslatableAttributes(), 'array')
        );
    }
}
