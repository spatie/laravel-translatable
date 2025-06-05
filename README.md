<div align="left">
    <a href="https://spatie.be/open-source?utm_source=github&utm_medium=banner&utm_campaign=laravel-translatable">
      <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://spatie.be/packages/header/laravel-translatable/html/dark.webp">
        <img alt="Logo for laravel-translatable" src="https://spatie.be/packages/header/laravel-translatable/html/light.webp">
      </picture>
    </a>

<h1>A trait to make Eloquent models translatable</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-translatable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-translatable)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-translatable/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-translatable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-translatable)
    
</div>

This package contains a trait `HasTranslations` to make Eloquent models translatable. Translations are stored as json. There is no extra table needed to hold them.

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;
    
    public $translatable = ['name']; // translatable attributes

    // ...
}
```

After the trait is applied on the model you can do these things:

```php
$newsItem = new NewsItem;
$newsItem
   ->setTranslation('name', 'en', 'Name in English')
   ->setTranslation('name', 'nl', 'Naam in het Nederlands')
   ->save();

$newsItem->name; // Returns 'Name in English' given that the current app locale is 'en'
$newsItem->getTranslation('name', 'nl'); // returns 'Naam in het Nederlands'

app()->setLocale('nl');
$newsItem->name; // Returns 'Naam in het Nederlands'

$newsItem->getTranslations('name'); // returns an array of all name translations

// You can translate nested keys of a JSON column using the -> notation
// First, add the path to the $translatable array, e.g., 'meta->description'
$newsItem
   ->setTranslation('meta->description', 'en', 'Description in English')
   ->setTranslation('meta->description', 'nl', 'Beschrijving in het Nederlands')
   ->save();

$attributeKey = 'meta->description';
$newsItem->$attributeKey; // Returns 'Description in English'
$newsItem->getTranslation('meta->description', 'nl'); // Returns 'Beschrijving in het Nederlands'
```

Also providing scoped queries for retrieving records based on locales

```php
// Returns all news items with a name in English
NewsItem::whereLocale('name', 'en')->get();

// Returns all news items with a name in English or Dutch
NewsItem::whereLocales('name', ['en', 'nl'])->get();

// Returns all news items that has name in English with value `Name in English` 
NewsItem::query()->whereJsonContainsLocale('name', 'en', 'Name in English')->get();

// Returns all news items that has name in English or Dutch with value `Name in English` 
NewsItem::query()->whereJsonContainsLocales('name', ['en', 'nl'], 'Name in English')->get();

// The last argument is the "operand" which you can tweak to achieve something like this:

// Returns all news items that has name in English with value like `Name in...` 
NewsItem::query()->whereJsonContainsLocale('name', 'en', 'Name in%', 'like')->get();

// Returns all news items that has name in English or Dutch with value like `Name in...` 
NewsItem::query()->whereJsonContainsLocales('name', ['en', 'nl'], 'Name in%', 'like')->get();
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-translatable.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-translatable)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/laravel-translatable).

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

We got the idea to store translations as json in a column from [Mohamed Said](https://github.com/themsaid). Parts of the readme of [his multilingual package](https://github.com/themsaid/laravel-multilingual) were used in this readme.

## Alternatives

- [DB-Fields-Translations](https://github.com/Afzaal565/DB-Fields-Translations)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
