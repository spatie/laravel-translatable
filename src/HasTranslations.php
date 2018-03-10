<?php

namespace Spatie\Translatable;

use Illuminate\Support\Str;
use Spatie\Translatable\Events\TranslationHasBeenSet;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;

trait HasTranslations
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, $this->getLocale());
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        // pass arrays and untranslatable attributes to the parent method
        if (!$this->isTranslatableAttribute($key) || is_array($value)) {
            return parent::setAttribute($key, $value);
        }
        // if the attribute is translatable and not already translated (=array),
        // set a translation for the current app locale
        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function translate(string $key, string $locale = '')
    {
        return $this->getTranslation($key, $locale);
    }

    /***
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     *
     * @return mixed
     */
    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true)
    {
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);

        $translations = $this->getTranslations($key);

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    public function getTranslationWithFallback(string $key, string $locale)
    {
        return $this->getTranslation($key, $locale, true);
    }

    public function getTranslationWithoutFallback(string $key, string $locale)
    {
        return $this->getTranslation($key, $locale, false);
    }

    public function getTranslations($key = null) : array
    {
        if ($key !== null) {
            $this->guardAgainstUntranslatableAttribute($key);

            return json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true) ?: [];
        }

        return array_reduce($this->getTranslatableAttributes(), function ($result, $item) {
            $result[$item] = $this->getTranslations($item);

            return $result;
        });
    }

    public function setTranslation(string $key, string $locale, $value): self
    {
        $this->guardAgainstUntranslatableAttribute($key);

        $translations = $this->getTranslations($key);

        $oldValue = $translations[$locale] ?? '';

        if ($this->hasSetMutator($key)) {
            $method = 'set'.Str::studly($key).'Attribute';
            $this->{$method}($value, $locale);
            $value = $this->attributes[$key];
        }

        $translations[$locale] = $value;

        $this->attributes[$key] = $this->asJson($translations);

        event(new TranslationHasBeenSet($this, $key, $locale, $oldValue, $value));

        return $this;
    }

    /**
     * @param string $key
     * @param array  $translations
     *
     * @return $this
     */
    public function setTranslations(string $key, array $translations)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $locale, $translation);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $locale
     *
     * @return $this
     */
    public function forgetTranslation(string $key, string $locale)
    {
        $translations = $this->getTranslations($key);

        unset($translations[$locale]);

        $this->setAttribute($key, $translations);

        return $this;
    }

    public function forgetAllTranslations(string $locale)
    {
        collect($this->getTranslatableAttributes())->each(function (string $attribute) use ($locale) {
            $this->forgetTranslation($attribute, $locale);
        });
    }

    public function getTranslatedLocales(string $key) : array
    {
        return array_keys($this->getTranslations($key));
    }

    public function isTranslatableAttribute(string $key) : bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    protected function guardAgainstUntranslatableAttribute(string $key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    protected function normalizeLocale(string $key, string $locale, bool $useFallbackLocale) : string
    {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }

        if (!$useFallbackLocale) {
            return $locale;
        }

        if (!is_null($fallbackLocale = config('translatable.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
    }

    protected function getLocale() : string
    {
        return config('app.locale');
    }

    public function getTranslatableAttributes() : array
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    public function getCasts() : array
    {
        return array_merge(
            parent::getCasts(),
            array_fill_keys($this->getTranslatableAttributes(), 'array')
        );
    }
}
