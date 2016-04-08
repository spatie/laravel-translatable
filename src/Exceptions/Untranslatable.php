<?php

namespace Spatie\Translatable\Exceptions;

use Exception;
use Spatie\Translatable\Translatable;

class Untranslatable extends Exception
{
    public static function attributeIsNotTranslatable(string $fieldName, Translatable $model)
    {
        $translatableFieldNames = implode(', ', $model->getTranslatableAttributes());

        return new static("Cannot translated field `{$fieldName}` as it does not one of the translatable fieldnames: `$translatableFieldNames`");
    }
}
