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
- Next, you should create a public property `$translatable` which holds an array with all the names of attributes you wish to make translatable.
- Finally, you should make sure that all translatable attributes are set to the `json`-datatype in your database. If your database doesn't support `json`-columns, use `text`.

Here's an example of a prepared model:

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;

    public array $translatable = ['name'];
}
