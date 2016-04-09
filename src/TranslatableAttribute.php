<?php

namespace Spatie\Translatable;

class TranslatableAttribute
{
    public $name;
    public $cast;

    public function __construct($key, $value)
    {
        if (is_string($key)) {
            $this->name = $key;
            $this->cast = $value;

            return;
        }

        $this->name = $value;
        $this->cast = 'string';
    }
}
