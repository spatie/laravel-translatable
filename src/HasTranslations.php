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

        return $this->getTranslation($key, config('app.locale'));
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
     *
     * @return mixed
     */
    public function getTranslation(string $key, string $locale)
    {
        $locale = $this->normalizeLocale($key, $locale);

        $translations = $this->getTranslations($key);

        $translation = $translations[$locale] ?? '';

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    public function getTranslations($key) : array
    {
        $this->guardAgainstUntranslatableAttribute($key);

        return json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true);
    }

    /**
     * @param string $key
     * @param string $locale
     * @param $value
     *
     * @return $this
     */
    public function setTranslation(string $key, string $locale, $value)
    {
        $this->guardAgainstUntranslatableAttribute($key);

        $translations = $this->getTranslations($key);

        $oldValue = $translations[$locale] ?? '';

        if ($this->hasSetMutator($key)) {
            $method = 'set'.Str::studly($key).'Attribute';
            $value = $this->{$method}($value);
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

    public function getTranslatedLocales(string $key) : array
    {
        return array_keys($this->getTranslations($key));
    }

    protected function isTranslatableAttribute(string $key) : bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    protected function guardAgainstUntranslatableAttribute(string $key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    protected function normalizeLocale(string $key, string $locale) : string
    {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }

        if (!is_null($fallbackLocale = config('laravel-translatable.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
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

    /**
     * Handle dynamic method calls into the model (hook for builder only).
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['where', 'orWhere'])) {
            if (in_array($parameters[0], $this->translatable)) {
                $parameters[0] = sprintf('%s->%s', $parameters[0], config('app.locale'));
            }
        } elseif (\Illuminate\Support\Str::startsWith($method, 'where')) {
            $finder = substr($method, 5);
            $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

            $connector = 'And';
            $query = $this->newQuery();
            foreach ($segments as $segment) {
                if ($segment != 'And' && $segment != 'Or') {
                    $method = ($connector == 'And') ? 'where' : 'orWhere';
                    $column = snake_case($segment);
                    if (in_array($column, $this->translatable)) {
                        $column = sprintf('%s->%s', $column, \App::getLocale());
                    }
                    $value = array_shift($parameters);
                    $query->$method($column, $value);
                } else {
                    $connector = $segment;
                }
            }
            return $query;
        }

        return parent::__call($method, $parameters);
    }
}
