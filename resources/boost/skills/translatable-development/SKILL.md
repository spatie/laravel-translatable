---
name: translatable-development
description: "Use when working with spatie/laravel-translatable. Trigger when the query mentions translatable models, translations, multi-language attributes, the HasTranslations trait, the #[Translatable] attribute, or storing translations as JSON on Eloquent models. Tasks include making a model translatable, writing migrations for translatable columns, setting translations for specific locales, getting translations (with or without fallbacks), querying by locale, handling missing translations, and testing translatable behavior."
license: MIT
metadata:
  author: spatie
---

# spatie/laravel-translatable

Translations are stored as JSON on the model itself. No extra tables, no pivot.

## Making a model translatable

Declare translatable attributes via the `#[Translatable]` PHP attribute (preferred) and add the `HasTranslations` trait.

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

The attribute accepts a variadic list of column names.

The `$translatable` property is also supported:

```php
class NewsItem extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'description'];
}
```

When both are present, their values are merged and deduplicated.

## Migration

Translatable columns must be `json` (or `text` if the database does not support `json`). One column per translatable attribute — the column holds all locales as a JSON object.

```php
Schema::create('news_items', function (Blueprint $table) {
    $table->id();
    $table->json('name')->nullable();
    $table->json('description')->nullable();
    $table->timestamps();
});
```

## Setting translations

```php
// Current locale (via app()->getLocale())
$newsItem->name = 'Hello';

// Specific locale
$newsItem->setTranslation('name', 'en', 'Hello');
$newsItem->setTranslation('name', 'nl', 'Hallo');

// Multiple locales at once
$newsItem->setTranslations('name', ['en' => 'Hello', 'nl' => 'Hallo']);

// Mass assignment with a per-locale array
NewsItem::create([
    'name' => ['en' => 'Hello', 'nl' => 'Hallo'],
]);

$newsItem->save();
```

## Getting translations

```php
// Current locale
$newsItem->name;

// Specific locale (falls back to the configured fallback locale by default)
$newsItem->getTranslation('name', 'nl');

// Without fallback
$newsItem->getTranslationWithoutFallback('name', 'nl');

// All translations for one attribute
$newsItem->getTranslations('name'); // ['en' => 'Hello', 'nl' => 'Hallo']

// All translations for all translatable attributes
$newsItem->translations;

// All locales this model has translations in
$newsItem->locales();
```

## Querying by locale

```php
// Models that have a translation for the given locale
NewsItem::query()->whereLocale('name', 'en')->get();

// Models that have a translation in any of the given locales
NewsItem::query()->whereLocales('name', ['en', 'nl'])->get();

// Filter by translated value
NewsItem::query()->whereJsonContainsLocale('name', 'en', 'Hello')->get();
```

## Forgetting translations

```php
$newsItem->forgetTranslation('name', 'nl');      // one locale of one attribute
$newsItem->forgetTranslations('name');           // all locales of one attribute
$newsItem->forgetAllTranslations('nl');          // one locale across all translatable attributes
```

## Fallback behavior

By default, `getTranslation()` falls back to `config('app.fallback_locale')` when a translation is missing. Override per model:

```php
class NewsItem extends Model
{
    use HasTranslations;

    public $useFallbackLocale = false; // disable fallback for this model

    public function getFallbackLocale(): ?string
    {
        return 'en'; // custom fallback
    }
}
```

## Testing

```php
use Spatie\Translatable\Facades\Translatable;

it('stores a translation', function () {
    $newsItem = NewsItem::create(['name' => ['en' => 'Hello']]);

    expect($newsItem->getTranslation('name', 'en'))->toBe('Hello');
});

it('falls back when a translation is missing', function () {
    config()->set('app.fallback_locale', 'en');

    $newsItem = NewsItem::create(['name' => ['en' => 'Hello']]);

    expect($newsItem->getTranslation('name', 'nl'))->toBe('Hello');
});
```

## Common pitfalls

- Translatable columns must be `json` (or `text`) in the migration. A `string` column will throw on JSON casting.
- Setting a plain string on a translatable attribute only writes the current locale. Pass an associative array to set multiple locales at once.
- Do not register an `array` or `json` cast on translatable columns in `$casts`. `HasTranslations` handles the JSON encoding.
- The `$translatable` property and `#[Translatable]` attribute are class-level. Runtime mutations of `$this->translatable` work but are uncommon.
- `getTranslation()` returns the fallback locale's value when the requested locale is missing. Use `getTranslationWithoutFallback()` for a strict lookup.
