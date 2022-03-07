---
title: Getting and setting translations
weight: 1
---

First, you must prepare your model as instructed in [the installation instructions](/docs/laravel-translatable/v6/installation-setup).

## Setting a translation

The easiest way to set a translation for the current locale is to just set the property for a translatable attribute.

Here's an example, given that `name` is a translatable attribute:

```php
$newsItem->name = 'New translation';
```

To actually save the translation, don't forget to save your model. 

```php
$newsItem->name = 'New translation'
$newsItem->save();
```

You can immediately set translations when creating a model.

```php
NewsItem::create([
   'name' => [
      'en' => 'Name in English',
      'nl' => 'Naam in het Nederlands'
   ],
]);
```

To set a translation for a specific locale you can use this method:

```php
public function setTranslation(string $attributeName, string $locale, string $value)
```

You can set translations for multiple languages with

```php
$translations = ['en' => 'hello', 'es' => 'hola'];
$newItem->name = $translations;

// alternatively, use the `setTranslations` method

$newsItem->setTranslations('hello', $translations);

$newItem->save();
```

## Getting a translation

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


### Getting all translations

You can get all translations by calling `getTranslations()` without an argument:

```php
$newsItem->getTranslations();
```

Or you can use the accessor:

```php
$yourModel->translations
```

The methods above will give you back an array that holds all translations, for example:

```php
$newsItem->getTranslations('name'); 
// returns ['en' => 'Name in English', 'nl' => 'Naam in het Nederlands']
```

The method above returns, all locales. If you only want specific locales, pass that to the second argument of `getTranslations`.

```php
public function getTranslations(string $attributeName, array $allowedLocales): array
```

Here's an example:

```php
$translations = [
    'en' => 'Hello',
    'fr' => 'Bonjour',
    'de' => 'Hallo',
];

$newsItem->setTranslations('hello', $translations);
$newsItem->getTranslations('hello', ['en', 'fr']); // returns ['en' => 'Hello', 'fr' => 'Bonjour']
```
