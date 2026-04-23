<?php

namespace Spatie\Translatable\Exceptions;

use Exception;

class AttributeIsNotTranslatable extends Exception
{
    public static function make(string $key, object $model): self
    {
        /** @var array<int, string> $translatableAttributes */
        $translatableAttributes = $model->getTranslatableAttributes();

        return new self("Cannot translate attribute `{$key}` as it's not one of the translatable attributes: `".implode(', ', $translatableAttributes).'`');
    }
}
