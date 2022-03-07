---
title: Replacing translations
weight: 3
---

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
