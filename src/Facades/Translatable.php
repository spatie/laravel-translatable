<?php

namespace Spatie\Translatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Translatable\Translatable
 *
 * @method static void fallback(?string $fallbackLocale = null, ?bool $fallbackAny = false, $missingKeyCallback = null)
 * @method static void allowNullForTranslation(bool $allowNullForTranslation = true)
 * @method static void allowEmptyStringForTranslation(bool $allowEmptyStringForTranslation = true)
 */
class Translatable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'translatable';
    }
}
