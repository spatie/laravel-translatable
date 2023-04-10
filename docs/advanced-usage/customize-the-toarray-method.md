---
title: Customize the toArray method
weight: 1
---

In many cases, the `toArray()` method on `Model` the class is called under the hood to serialize your model.

To customize for all your models what should get returned for the translatable attributes you could wrap the`Spatie\Translatable\HasTranslations` trait into a custom trait and overrides the `toArray()` method.

```php
namespace App\Traits;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations;

    public function toArray()
    {
        $attributes = $this->attributesToArray(); // attributes selected by the query
        // remove attributes if they are not selected
        $translatables = array_filter($this->getTranslatableAttributes(), function ($key) use ($attributes) {
            return array_key_exists($key, $attributes);
        });
        foreach ($translatables as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }
        return array_merge($attributes, $this->relationsToArray());
    }
}
```
