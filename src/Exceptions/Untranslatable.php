<?php

namespace Spatie\Translatable\Exceptions;

use Exception;
use Spatie\Translatable\Translatable;

class Untranslatable extends Exception
{
    public static function fieldIsNotTranslatable(string $fieldName, Translatable $model)
    {
        $translatableFieldNames = implode(',', $model->getTranslatableFields());

        return new static("Cannot translated field `{$fieldName}` as it does not one of the translatable fieldnames: `$translatableFieldNames`");
    }
}