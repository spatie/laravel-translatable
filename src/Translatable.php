<?php

namespace Spatie\Translatable;

class Translatable
{
    public $missingKeyCallback;

    public function fallback($missingKeyCallback): self
    {
       $this->missingKeyCallback = $missingKeyCallback;

        return $this;
    }
}
