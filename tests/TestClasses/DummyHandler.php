<?php

namespace Spatie\Translatable\Test\TestClasses;

use Spatie\Translatable\FallbackCallback;

class DummyHandler implements FallbackCallback
{
    public static function missingKeyHandler($model, string $translationKey, string $locale): void
    {
        //do something assertable
    }
}
