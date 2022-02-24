<?php

namespace Spatie\Translatable;

interface FallbackCallback
{
    public static function missingKeyHandler($model, string $translationKey, string $locale): void;
}
