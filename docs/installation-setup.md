---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-translatable
```

## Making a model translatable

The required steps to make a model translatable are:

- First, you need to add the `Spatie\Translatable\HasTranslations`-trait.
- Next, you should declare which attributes are translatable using the `#[Translatable]` PHP attribute.
- Finally, you should make sure that all translatable attributes are set to the `json`-datatype in your database. If your database doesn't support `json`-columns, use `text`.

Here's an example of a prepared model:

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable('name', 'description')]
class NewsItem extends Model
{
    use HasTranslations;
}
```

The attribute accepts a variadic list of column names, so you can pass as many as you need.

Alternatively, you can declare the translatable attributes via a public `$translatable` property:

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];
}
```

When both the property and the attribute are present, their values are merged and deduplicated.
