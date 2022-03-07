---
title: Removing translations
weight: 2
---

You can forget a translation for a specific field using the `forgetTranslation` function:

```php
public function forgetTranslation(string $attributeName, string $locale)
```

Here's an example:

```php
$newsItem->forgetTranslation('name', 'nl');
```

You can forget all translations for a specific locale:

```php
public function forgetAllTranslations(string $locale)
```

Here's an example:

```php
$newsItem->forgetTranslation('name');
```
