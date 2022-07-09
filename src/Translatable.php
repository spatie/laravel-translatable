<?php

namespace Spatie\Translatable;

use Closure;

class Translatable
{
    /*
     * If a translation has not been set for a given locale, use this locale instead.
     */
    protected ?string $fallbackLocale;

    /*
     * If a translation has not been set for a given locale and the fallback locale,
     * any other locale will be chosen instead.
     */
    protected bool $fallbackAny = false;

    protected ?Closure $missingKeyCallback = null;

    public function fallback(
        ?string $fallbackLocale = null,
        ?bool $fallbackAny = false,
        $missingKeyCallback = null
    ): self {
        $this->fallbackLocale = $fallbackLocale;
        $this->fallbackAny = $fallbackAny;
        $this->missingKeyCallback = $missingKeyCallback;

        return $this;
    }
}
