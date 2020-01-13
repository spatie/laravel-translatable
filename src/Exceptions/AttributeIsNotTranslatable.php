<?php

namespace Spatie\Translatable\Exceptions;

use Exception;

class AttributeIsNotTranslatable extends Exception
{
    public static function make(string $key, $model)
    {
        $translatableAttributes = implode(', ', $model->getTranslatableAttributes());

        return new static("Cannot translate attribute `{$key}` as it's not one of the translatable attributes: `$translatableAttributes`");
    }
}
