<?php

namespace Spatie\Translatable;

use Closure;

class Translatable
{
    /*
     * If a translation has not been set for a given locale, use this locale instead.
     */
    public ?string $fallbackLocale;

    /*
     * If a translation has not been set for a given locale and the fallback locale,
     * any other locale will be chosen instead.
     */
    public bool $fallbackAny = false;

    public ?Closure $missingKeyCallback = null;

    public bool $allowNullForTranslation = false;

    public bool $allowEmptyStringForTranslation = false;

    public function fallback(
        ?string $fallbackLocale = null,
        ?bool $fallbackAny = false,
        ?Closure $missingKeyCallback = null
    ): self {
        $this->fallbackLocale = $fallbackLocale;
        $this->fallbackAny = $fallbackAny;
        $this->missingKeyCallback = $missingKeyCallback;

        return $this;
    }

    public function allowNullForTranslation(bool $allowNullForTranslation = true): self
    {
        $this->allowNullForTranslation = $allowNullForTranslation;

        return $this;
    }

    public function allowEmptyStringForTranslation(bool $allowEmptyStringForTranslation = true): self
    {
        $this->allowEmptyStringForTranslation = $allowEmptyStringForTranslation;

        return $this;
    }
}
