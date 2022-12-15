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
