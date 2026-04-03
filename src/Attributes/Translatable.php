<?php

namespace Spatie\Translatable\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Translatable
{
    public array $columns;

    public function __construct(array|string ...$columns)
    {
        $this->columns = is_array($columns[0] ?? null) ? $columns[0] : $columns;
    }
}
