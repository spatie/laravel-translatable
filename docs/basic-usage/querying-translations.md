---
title: Querying translations
weight: 5
---

If you're using MySQL 5.7 or above, it's recommended that you use the JSON data type for housing translations in the db.
This will allow you to query these columns like this:

```php
NewsItem::where('name->en', 'Name in English')->get();
```

Or if you're using MariaDB 10.2.3 or above :
```php
NewsItem::whereRaw("JSON_EXTRACT(name, '$.en') = 'Name in English'")->get();
```

If you want to query records based on locales, you can use the `whereLocale` and `whereLocales` methods.

```php
NewsItem::whereLocale('name', 'en')->get(); // Returns all news items with a name in English

NewsItem::whereLocales('name', ['en', 'nl'])->get(); // Returns all news items with a name in English or Dutch
```

If you want to query records based on locale's value, you can use the `whereJsonContainsLocale` and `whereJsonContainsLocales` methods.

```php
// Returns all news items that has name in English with value `Name in English` 
NewsItem::query()->whereJsonContainsLocale('name', 'en', 'Name in English')->get(); 

// Returns all news items that has name in English or Dutch with value `Name in English` 
NewsItem::query()->whereJsonContainsLocales('name', ['en', 'nl'], 'Name in English')->get(); 
```
