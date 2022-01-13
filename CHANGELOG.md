# Changelog

All notable changes to `laravel-translatable` will be documented in this file

## 5.2.0 - 2022-01-13

- support Laravel 9

## 5.0.3 - 2021-10-04

- solve the string value issue in filterTranslations method (#300)

## 5.0.2 - 2021-09-28

- specify locales in get translations method (#299)

## 5.0.1 - 2021-07-15

- fix return types of getTranslation (#286)

## 5.0.0 - 2021-03-26

- require PHP 8+
- convert syntax to PHP 8
- drop support for PHP 7.x
- drop support for Laravel 6.x
- implement `spatie/laravel-package-tools`

## 4.6.0 - 2020-11-19

- add support for PHP 8.0 (#241)
- drop support for Laravel 5.8 (#241)

## 4.5.2 - 2020-10-22

- revert #235

## 4.5.1 - 2020-10-22

- use string casting for translatable columns (#235)

## 4.5.0 2020-10-03

- add replaceTranslations method (#231)

## 4.4.3 - 2020-10-2

- rename `withLocale` to `usingLocale`

## 4.4.2 - 2020-10-02

- elegant syntax update (#229)

## 4.4.1 - 2020-09-06

- add support for Laravel 8 (#226)

## 4.4.0 - 2020-07-09

- make possible to set multiple translations on mutator model field with array (#216)

## 4.3.2 - 2020-04-30

- fix `forgetTranslation` & `forgetAllTranslations` on fields with mutator (#205)

## 4.3.1 - 2020-03-07

- Lumen fix (#201)

## 4.3.0 - 2020-03-02

- add support for Laravel 7

## 4.2.2 - 2020-01-20

- open up for non-model objects (#186)

## 4.2.1 - 2019-10-03

- add third param to translate method (#177)

## 4.2.0 - 2019-09-04

- make compatible with Laravel 6

## 4.1.4 - 2019-08-28

- re-added the `translatable.fallback_local` config which overrule `app.fallback_local` (see https://github.com/spatie/laravel-translatable/issues/170)

## 4.1.3 - 2019-06-16

- improve dependencies

## 4.1.2 - 2019-06-06

- allow false and true values in translations

## 4.1.1 - 2019-02-27

- fix service provider error

## 4.1.0 - 2019-02-27

- drop support for Laravel 5.7 and below
- drop support for PHP 7.1 and below

## 4.0.0 - 2019-02-27

- `app.fallback_local` will now be used (see #148)

## 3.1.3 - 2019-02-27

- add support for Laravel 5.8

## 3.1.2 - 2019-01-05

- add `hasTranslation`

## 3.1.1 - 2018-12-18

- allow 0 to be used as a translation value

## 3.1.0 - 2018-11-29

- allow `getTranslations` to return other things than strings

## 3.0.1 - 2018-09-18

- fix regarding empty locales

## 3.0.0 - 2018-09-16

- added `translations` accessor
- dropped support for PHP 7.0

## 2.2.1 - 2018-08-24

- add support for Laravel 5.7

## 2.2.0 - 2018-03-09

- made it possible to get all translations in one go

## 2.1.5 - 2018-02-28

- better handling of `null` values

## 2.1.4 - 2018-02-08

- add support for L5.6

## 2.1.3 - 2018-01-24

- make locale handling more flexible

## 2.1.2 - 2017-12-24

- fix for using translations within translations

## 2.1.1 - 2017-12-20

- fix event `key` attribute
- fix support for mutators

## 2.1.0 - 2017-09-21

- added support for setting a translation directly through the property

## 2.0.0 - 2017-08-30

- added support for Laravel 5.5, dropped support for all older versions
- rename config file from `laravel-translatable` to `translatable`

## 1.3.0 - 2017-06-12

- add `forgetAllTranslations`

## 1.2.2 - 2016-01-27

- improve support for fallback locale

## 1.2.1 - 2016-01-23

- improve compatibility for Laravel 5.4

## 1.2.0 - 2016-01-23

- add compatibility for Laravel 5.4

## 1.1.2 - 2016-10-02

- made `isTranslatableAttribute` public

## 1.1.1 - 2016-08-24

- add L5.3 compatibility

## 1.1.0 - 2016-05-02

- added support for a fallback locale

## 1.0.0 - 2016-04-10

- initial release
