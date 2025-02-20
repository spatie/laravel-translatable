<?php

namespace Spatie\Translatable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\Translatable\Events\TranslationHasBeenSetEvent;
use Spatie\Translatable\Exceptions\AttributeIsNotTranslatable;

trait HasTranslations
{
    protected ?string $translationLocale = null;

    public function initializeHasTranslations(): void
    {
        $this->mergeCasts(
            array_fill_keys($this->getTranslatableAttributes(), 'array'),
        );
    }

    public static function usingLocale(string $locale): self
    {
        return (new self)->setLocale($locale);
    }

    public function useFallbackLocale(): bool
    {
        if (property_exists($this, 'useFallbackLocale')) {
            return $this->useFallbackLocale;
        }

        return true;
    }

    public function getAttributeValue($key): mixed
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, $this->getLocale(), $this->useFallbackLocale());
    }

    protected function mutateAttributeForArray($key, $value): mixed
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::mutateAttributeForArray($key, $value);
        }

        $translations = $this->getTranslations($key);

        return array_map(fn ($value) => parent::mutateAttributeForArray($key, $value), $translations);
    }

    public function setAttribute($key, $value)
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::setAttribute($key, $value);
        }

        if (is_array($value) && (! array_is_list($value) || count($value) === 0)) {
            return $this->setTranslations($key, $value);
        }

        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    public function translate(string $key, string $locale = '', bool $useFallbackLocale = true): mixed
    {
        return $this->getTranslation($key, $locale, $useFallbackLocale);
    }

    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true): mixed
    {
        $normalizedLocale = $this->normalizeLocale($key, $locale, $useFallbackLocale);

        $isKeyMissingFromLocale = ($locale !== $normalizedLocale);

        $translations = $this->getTranslations($key);

        $baseKey = Str::before($key, '->'); // get base key in case it is JSON nested key

        $translatableConfig = app(Translatable::class);

        if (is_null(self::getAttributeFromArray($baseKey))) {
            $translation = null;
        } else {
            $translation = isset($translations[$normalizedLocale]) ? $translations[$normalizedLocale] : null;
            $translation ??= ($translatableConfig->allowNullForTranslation) ? null : '';
        }

        if ($isKeyMissingFromLocale && $translatableConfig->missingKeyCallback) {
            try {
                $callbackReturnValue = ($translatableConfig->missingKeyCallback)($this, $key, $locale, $translation, $normalizedLocale);
                if (is_string($callbackReturnValue)) {
                    $translation = $callbackReturnValue;
                }
            } catch (Exception) {
                // prevent the fallback to crash
            }
        }

        $key = str_replace('->', '-', $key);

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        if ($this->hasAttributeMutator($key)) {
            return $this->mutateAttributeMarkedAttribute($key, $translation);
        }

        return $translation;
    }

    public function getTranslationWithFallback(string $key, string $locale): mixed
    {
        return $this->getTranslation($key, $locale, true);
    }

    public function getTranslationWithoutFallback(string $key, string $locale): mixed
    {
        return $this->getTranslation($key, $locale, false);
    }

    public function getTranslations(?string $key = null, ?array $allowedLocales = null): array
    {
        if ($key !== null) {
            $this->guardAgainstNonTranslatableAttribute($key);
            $translatableConfig = app(Translatable::class);

            if ($this->isNestedKey($key)) {
                [$key, $nestedKey] = explode('.', str_replace('->', '.', $key), 2);
            }

            return array_filter(
                Arr::get($this->fromJson($this->getAttributeFromArray($key)), $nestedKey ?? null, []),
                fn ($value, $locale) => $this->filterTranslations($value, $locale, $allowedLocales, $translatableConfig->allowNullForTranslation, $translatableConfig->allowEmptyStringForTranslation),
                ARRAY_FILTER_USE_BOTH,
            );
        }

        return array_reduce($this->getTranslatableAttributes(), function ($result, $item) use ($allowedLocales) {
            $result[$item] = $this->getTranslations($item, $allowedLocales);

            return $result;
        });
    }

    public function setTranslation(string $key, string $locale, $value): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        $translations = $this->getTranslations($key);

        $oldValue = $translations[$locale] ?? '';

        $mutatorKey = str_replace('->', '-', $key);

        if ($this->hasSetMutator($mutatorKey)) {
            $method = 'set'.Str::studly($mutatorKey).'Attribute';

            $this->{$method}($value, $locale);

            $value = $this->attributes[$key];
        } elseif ($this->hasAttributeSetMutator($mutatorKey)) { // handle new attribute mutator
            $this->setAttributeMarkedMutatedAttributeValue($mutatorKey, $value);

            $value = $this->attributes[$mutatorKey];
        }

        $translations[$locale] = $value;

        if ($this->isNestedKey($key)) {
            unset($this->attributes[$key], $this->attributes[$mutatorKey]);

            $this->fillJsonAttribute($key, $translations);
        } else {
            $this->attributes[$key] = json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        event(new TranslationHasBeenSetEvent($this, $key, $locale, $oldValue, $value));

        return $this;
    }

    public function setTranslations(string $key, array $translations): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        if (! empty($translations)) {
            foreach ($translations as $locale => $translation) {
                $this->setTranslation($key, $locale, $translation);
            }
        } else {
            $this->attributes[$key] = $this->asJson([]);
        }

        return $this;
    }

    public function forgetTranslation(string $key, string $locale): self
    {
        $translations = $this->getTranslations($key);

        unset(
            $translations[$locale],
            $this->$key
        );

        $this->setTranslations($key, $translations);

        return $this;
    }

    public function forgetTranslations(string $key, bool $asNull = false): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        collect($this->getTranslatedLocales($key))->each(function (string $locale) use ($key) {
            $this->forgetTranslation($key, $locale);
        });

        if ($asNull) {
            $this->attributes[$key] = null;
        }

        return $this;
    }

    public function forgetAllTranslations(string $locale): self
    {
        collect($this->getTranslatableAttributes())->each(function (string $attribute) use ($locale) {
            $this->forgetTranslation($attribute, $locale);
        });

        return $this;
    }

    public function getTranslatedLocales(string $key): array
    {
        return array_keys($this->getTranslations($key));
    }

    public function isNestedKey(string $key): bool
    {
        return str_contains($key, '->');
    }

    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    public function hasTranslation(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?: $this->getLocale();

        return isset($this->getTranslations($key)[$locale]);
    }

    public function replaceTranslations(string $key, array $translations): self
    {
        foreach ($this->getTranslatedLocales($key) as $locale) {
            $this->forgetTranslation($key, $locale);
        }

        $this->setTranslations($key, $translations);

        return $this;
    }

    protected function guardAgainstNonTranslatableAttribute(string $key): void
    {
        if (! $this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    protected function normalizeLocale(string $key, string $locale, bool $useFallbackLocale): string
    {
        $translatedLocales = $this->getTranslatedLocales($key);

        if (in_array($locale, $translatedLocales)) {
            return $locale;
        }

        if (! $useFallbackLocale) {
            return $locale;
        }

        if (method_exists($this, 'getFallbackLocale')) {
            $fallbackLocale = $this->getFallbackLocale();
        }

        $fallbackConfig = app(Translatable::class);

        $fallbackLocale ??= $fallbackConfig->fallbackLocale ?? config('app.fallback_locale');

        if (! is_null($fallbackLocale) && in_array($fallbackLocale, $translatedLocales)) {
            return $fallbackLocale;
        }

        if (! empty($translatedLocales) && $fallbackConfig->fallbackAny) {
            return $translatedLocales[0];
        }

        return $locale;
    }

    protected function filterTranslations(mixed $value = null, ?string $locale = null, ?array $allowedLocales = null, bool $allowNull = false, bool $allowEmptyString = false): bool
    {
        if ($value === null && ! $allowNull) {
            return false;
        }

        if ($value === '' && ! $allowEmptyString) {
            return false;
        }

        if ($allowedLocales === null) {
            return true;
        }

        if (! in_array($locale, $allowedLocales)) {
            return false;
        }

        return true;
    }

    public function setLocale(string $locale): self
    {
        $this->translationLocale = $locale;

        return $this;
    }

    public function getLocale(): string
    {
        return $this->translationLocale ?: config('app.locale');
    }

    public function getTranslatableAttributes(): array
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    public function translations(): Attribute
    {
        return Attribute::get(function () {
            return collect($this->getTranslatableAttributes())
                ->mapWithKeys(function (string $key) {
                    return [$key => $this->getTranslations($key)];
                })
                ->toArray();
        });
    }

    public function locales(): array
    {
        return array_unique(
            array_reduce($this->getTranslatableAttributes(), function ($result, $item) {
                return array_merge($result, $this->getTranslatedLocales($item));
            }, [])
        );
    }

    public function scopeWhereLocale(Builder $query, string $column, string $locale): void
    {
        $query->whereNotNull("{$column}->{$locale}");
    }

    public function scopeWhereLocales(Builder $query, string $column, array $locales): void
    {
        $query->where(function (Builder $query) use ($column, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereNotNull("{$column}->{$locale}");
            }
        });
    }

    public function scopeWhereJsonContainsLocale(Builder $query, string $column, string $locale, mixed $value, string $operand = '='): void
    {
        $query->where("{$column}->{$locale}", $operand, $value);
    }

    public function scopeWhereJsonContainsLocales(Builder $query, string $column, array $locales, mixed $value, string $operand = '='): void
    {
        $query->where(function (Builder $query) use ($column, $locales, $value, $operand) {
            foreach ($locales as $locale) {
                $query->orWhere("{$column}->{$locale}", $operand, $value);
            }
        });
    }

    /**
     * @deprecated
     */
    public static function whereLocale(string $column, string $locale): Builder
    {
        return static::query()->whereNotNull("{$column}->{$locale}");
    }

    /**
     * @deprecated
     */
    public static function whereLocales(string $column, array $locales): Builder
    {
        return static::query()->where(function (Builder $query) use ($column, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereNotNull("{$column}->{$locale}");
            }
        });
    }
}
