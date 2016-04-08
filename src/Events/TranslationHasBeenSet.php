<?php

namespace Spatie\Translatable\Events;

use Spatie\Translatable\Translatable;

class TranslationHasBeenSet
{
    /** @var \Spatie\Translatable\Translatable */
    public $model;

    /** @var string  */
    public $attributeName;

    /** @var string  */
    public $locale;

    public $oldValue;
    public $newValue;

    public function __construct(Translatable $model, string $attributeName, string $locale, $oldValue, $newValue)
    {
        $this->model = $model;

        $this->attributeName = $attributeName;

        $this->locale = $locale;

        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }
}
