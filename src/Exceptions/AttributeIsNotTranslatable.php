<?php

namespace Spatie\Translatable\Exceptions;

use Exception;
use Spatie\Translatable\Translatable;

class AttributeIsNotTranslatable extends Exception
{
    public static function make(string $fieldName, Translatable $model)
    {
        $translatableFieldNames = implode(', ', $model->getTranslatableAttributes());

        return new static("Cannot translated field `{$fieldName}` as it does not one of the translatable fieldnames: `$translatableFieldNames`");
    }
}
