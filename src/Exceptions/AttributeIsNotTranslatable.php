<?php

namespace Spatie\Translatable\Exceptions;

use Exception;
use Spatie\Translatable\Translatable;

class AttributeIsNotTranslatable extends Exception
{
    public static function make(string $key, Translatable $model)
    {
        $translatableAttributes = implode(', ', $model->getTranslatableAttributes());

        return new static("Cannot translated attribute `{$key}` as it does not one of the translatable attributes: `$translatableAttributes`");
    }
}
