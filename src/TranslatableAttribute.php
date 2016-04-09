<?php

namespace Spatie\Translatable;

use Spatie\Translatable\Exceptions\InvalidCast;

class TranslatableAttribute
{
    public $name;
    public $cast;

    public function __construct($key, $value)
    {
        $this->name = $this->determineName($key, $value);
        $this->cast = $this->determineCast($key, $value);
    }

    protected function determineName($key, $value) : string
    {
        return is_string($key) ? $key : $value;
    }

    protected function determineCast($key, $value) : string
    {
        $cast = is_string($key) ? $value : 'string';

        $availableCasts = [
            'string',
            'bool',
            'integer',
            'float',
            'array',
        ];

        if (!in_array($cast, $availableCasts)) {
            throw InvalidCast::make($cast, $availableCasts);
        }

        return $cast;
    }
}
