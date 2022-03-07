<?php

namespace Spatie\Translatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Translatable\Translatable
 */
class Translatable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'translatable';
    }
}
