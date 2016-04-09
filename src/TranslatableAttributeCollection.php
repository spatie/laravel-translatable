<?php

namespace Spatie\Translatable;

use Illuminate\Support\Collection;

class TranslatableAttributeCollection extends Collection
{
    public static function createForModel(Translatable $model)
    {
        $translatableFieldCollection = new static();

        foreach ($model->getTranslatableAttributes() as $key => $value) {
            $translatableFieldCollection->push(new TranslatableAttribute($key, $value));
        }

        return $translatableFieldCollection;
    }

    protected function find(string $attributeName)
    {
        foreach ($this->items as $translatableAttribute) {
            if ($translatableAttribute->name == $attributeName) {
                return $translatableAttribute;
            };
        }

        return;
    }

    public function isTranslatable(string $attributeName) : bool
    {
        return !is_null($this->find($attributeName));
    }

    public function getCast(string $attributeName) : string
    {
        $translatableAttribute = $this->find($attributeName);

        if (!$translatableAttribute) {
            // throw exception
        }

        return $this->find($attributeName)->cast;
    }
}
