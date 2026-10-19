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

To search for a partial value in one or more translated columns across locales, use `whereTranslationLike`:

```php
NewsItem::query()->whereTranslationLike(['name', 'body'], 'news', ['en', 'nl'])->get();
```

The scope matches any of the requested columns and locales, and groups these matches so they can be combined with other query constraints. It searches translated values, not locale keys.

When locales are omitted, it uses the locales configured via the `Spatie\Translatable\Facades\Translatable` facade, falling back to the model's current locale:

```php
Translatable::locales(['en', 'nl']);

NewsItem::query()->whereTranslationLike('name', 'news')->get();
```

Explicit locales override the configured defaults. Passing an empty columns or locales array matches no records.

Both the translated value and the search pattern are lowercased by the database. Unicode case conversion depends on your database and collation; SQLite's built-in `lower()` only converts ASCII characters. Non-ASCII values can still be searched, but their letter case must match on SQLite unless Unicode-aware functions are installed.

The search value is bound as a parameter and wrapped in `%` wildcards. Any `%` and `_` characters in the value retain their SQL `LIKE` wildcard meaning. An empty search matches any non-null translated value in the requested columns and locales. Columns and locales use Laravel's JSON selector syntax and should be application-defined, not taken directly from untrusted request input.
