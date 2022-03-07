---
title: Upgrading
weight: 3
---

### From v5 to v6

The config file has been removed. You can now define a fallback locale, set `fallBackAny` and handle custom behaviour for missing translations, via `Translatable::fallback()`. Take a look in the readme to learn how to specify the fallback behaviour you want.

The `TranslationHasBeenSet` event has been renamed to `TranslationHasBeenSetEvent`.

### From v2 to v3

In most cases you can upgrade without making any changes to your codebase at all. `v3` introduced a `translations` accessor on your models. If you already had one defined on your model, you'll need to rename it.

