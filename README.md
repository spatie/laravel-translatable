# A trait to make Eloquent models translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-translatable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-translatable)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-translatable/run-tests?label=tests)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-translatable.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-translatable)

This package contains a trait to make Eloquent models translatable. Translations are stored as json. There is no extra table needed to hold them.

Once the trait is installed on the model you can do these things:

```php
$newsItem = new NewsItem; // This is an Eloquent model
$newsItem
   ->setTranslation('name', 'en', 'Name in English')
   ->setTranslation('name', 'nl', 'Naam in het Nederlands')
   ->save();
   
$newsItem->name; // Returns 'Name in English' given that the current app locale is 'en'
$newsItem->getTranslation('name', 'nl'); // returns 'Naam in het Nederlands'

app()->setLocale('nl');

$newsItem->name; // Returns 'Naam in het Nederlands'
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-translatable.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-translatable)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

``` bash
composer require spatie/laravel-translatable
```

If you want to have another fallback_locale than the app fallback locale (see `config/app.php`), you could publish the config file:
```
php artisan vendor:publish --provider="Spatie\Translatable\TranslatableServiceProvider"
```

This is the contents of the published file:
```php
return [
  'fallback_locale' => 'en',
];
```

## Making a model translatable

The required steps to make a model translatable are:

- First, you need to add the `Spatie\Translatable\HasTranslations`-trait.
- Next, you should create a public property `$translatable` which holds an array with all the names of attributes you wish to make translatable.
- Finally, you should make sure that all translatable attributes are set to the `text`-datatype in your database. If your database supports `json`-columns, use that.

Here's an example of a prepared model:

``` php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;
    
    public $translatable = ['name'];
}
```

### Available methods

#### Getting a translation

The easiest way to get a translation for the current locale is to just get the property for the translated attribute.
For example (given that `name` is a translatable attribute):

```php
$newsItem->name;
```

You can also use this method:

```php
public function getTranslation(string $attributeName, string $locale, bool $useFallbackLocale = true) : string
```

This function has an alias named `translate`.

#### Getting all translations

You can get all translations by calling `getTranslations()` without an argument:

```php
$newsItem->getTranslations();
```

Or you can use the accessor

```php
$yourModel->translations
```

#### Setting a translation
The easiest way to set a translation for the current locale is to just set the property for a translatable attribute.
For example (given that `name` is a translatable attribute):

```php
$newsItem->name = 'New translation';
```

Also you can set translations with

```php
$newItem->name = ['en' => 'myName', 'nl' => 'Naam in het Nederlands'];
```

To set a translation for a specific locale you can use this method:

``` php
public function setTranslation(string $attributeName, string $locale, string $value)
```

To actually save the translation, don't forget to save your model.

```php
$newsItem->setTranslation('name', 'en', 'Updated name in English');

$newsItem->save();
```

#### Validation

- if you want to validate an attribute for uniqueness before saving/updating the db, you might want to have a look at [laravel-unique-translation](https://github.com/codezero-be/laravel-unique-translation) which is made specifically for *laravel-translatable*.

#### Forgetting a translation

You can forget a translation for a specific field:
``` php
public function forgetTranslation(string $attributeName, string $locale)
```

You can forget all translations for a specific locale:
``` php
public function forgetAllTranslations(string $locale)
```

#### Getting all translations in one go

``` php
public function getTranslations(string $attributeName): array
```

#### Setting translations in one go

``` php
public function setTranslations(string $attributeName, array $translations)
```

Here's an example:

``` php
$translations = [
   'en' => 'Name in English',
   'nl' => 'Naam in het Nederlands'
];

$newsItem->setTranslations('name', $translations);
```

#### Replace translations in one go

You can replace all the translations for a single key using this method:

```php
public function replaceTranslations(string $key, array $translations)
```

Here's an example:

```php
$translations = ['en' => 'hello', 'es' => 'hola'];

$newsItem->setTranslations('hello', $translations);
$newsItem->getTranslations(); // ['en' => 'hello', 'es' => 'hola']

$newTranslations = ['en' => 'hello'];

$newsItem->replaceTranslations('hello', $newTranslations);
$newsItem->getTranslations(); // ['en' => 'hello']
```

#### Setting the model locale
The default locale used to translate models is the application locale,
however it can sometimes be handy to use a custom locale.  

To do so, you can use `setLocale` on a model instance.
``` php
$newsItem = NewsItem::firstOrFail()->setLocale('fr');

// Any properties on this model will automaticly be translated in French
$newsItem->name; // Will return `fr` translation
$newsItem->name = 'Actualité'; // Will set the `fr` translation
```

Alternatively, you can use `usingLocale` static method:
``` php
// Will automatically set the `fr` translation
$newsItem = NewsItem::usingLocale('fr')->create([
    'name' => 'Actualité',
]);
```

### Events

#### TranslationHasBeenSet
Right after calling `setTranslation` the `Spatie\Translatable\Events\TranslationHasBeenSet`-event will be fired.

It has these properties:
```php
/** @var \Illuminate\Database\Eloquent\Model */
public $model;

/** @var string  */
public $attributeName;

/** @var string  */
public $locale;

public $oldValue;
public $newValue;
```

### Creating models

You can immediately set translations when creating a model. Here's an example:
```php
NewsItem::create([
   'name' => [
      'en' => 'Name in English',
      'nl' => 'Naam in het Nederlands'
   ],
]);
```

### Querying translatable attributes

If you're using MySQL 5.7 or above, it's recommended that you use the json data type for housing translations in the db.
This will allow you to query these columns like this:

```php
NewsItem::where('name->en', 'Name in English')->get();
```

Or if you're using MariaDB 10.2.3 or above :
```php
NewsItem::whereRaw("JSON_EXTRACT(name, '$.en') = 'Name in English'")->get();
```

### Automatically display the right translation when displaying model

Many times models using `HasTranslation` trait may be directly returned as response content.
In this scenario, and similar ones, the `toArray()` method on `Model` class is called under the hood to serialize your model; it accesses directly the $attributes field to perform the serialization, bypassing the translatable feature (which is based on accessors and mutators) and returning the text representation of the stored JSON instead of the translated value.

The best way to make your model automatically return translated fields is to wrap `Spatie\Translatable\HasTranslations` trait into a custom trait which overrides the `toArray()` method to automatically replace all translatable fields content with their translated value, like in the following example, and use it instead of the default one.

``` php
namespace App\Traits;
use Spatie\Translatable\HasTranslations as BaseHasTranslations;
trait HasTranslations
{
    use BaseHasTranslations;
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }
        return $attributes;
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Upgrading 

### From v2 to v3

In most cases you can upgrade without making any changes to your codebase at all. `v3` introduced a `translations` accessor on your models. If you already had one defined on your model, you'll need to rename it.

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [All Contributors](../../contributors)

We got the idea to store translations as json in a column from [Mohamed Said](https://github.com/themsaid). Parts of the readme of [his multilingual package](https://github.com/themsaid/laravel-multilingual) were used in this readme.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
