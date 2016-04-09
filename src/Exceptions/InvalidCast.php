<?php

namespace Spatie\Translatable\Exceptions;

use Exception;

class InvalidCast extends Exception
{
    public static function make(string $cast, array $availableCasts)
    {
        $availableCasts = implode(', ', $availableCasts);

        return new static("Cannot cast to type `{$cast}`. Available casts are: {$availableCasts}");
    }
}
