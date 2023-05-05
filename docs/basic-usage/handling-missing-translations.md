---
title: Handling missing translations 
weight: 7
---

Sometimes your model doesn't have a requested translation. Using the fallback functionality, you can decide what should
happen.

To set up fallback you need to call static method on the facade `Spatie\Translatable\Facades\Translatable`. Typically,
you would put this
in [a service provider of your own](https://laravel.com/docs/8.x/providers#writing-service-providers):

```php
    // typically, in a service provider
        
    use Spatie\Translatable\Facades\Translatable;
    
    Translatable::fallback(
        ...
    );
```

### Falling back to a specific locale

If you want to have another `fallback_locale` than the app fallback locale (see `config/app.php`), you should pass it
as `$fallbackLocale` parameter:

```php
    use Spatie\Translatable\Facades\Translatable;

    Translatable::fallback(
        fallbackLocale: 'fr',
    );
```

### Fallback locale per model

If the fallback locale differs between models, you can define a `getFallbackLocale()` method on your model.

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;

    public $fillable = ['name', 'fallback_locale'];

    public $translatable = ['name'];

    public function getFallbackLocale() : string
    {
        return $this->fallback_locale;
    }
}
```

### Falling back to any locale

Sometimes it is favored to return any translation if neither the translation for the preferred locale nor the fallback
locale are set. To do so, just pass `$fallbackAny` to true:

```php
    use Spatie\Translatable\Facades\Translatable;

    Translatable::fallback(
         fallbackAny: true,
    );
```

### Customize fallbacks

You can set up a fallback callback that is called when a translation key is missing/not found. It just lets you execute
some custom code like logging something or contact a remote service for example.

You have to register some code you want to run, by passing a closure to `$missingKeyCallback`.

Here's an example with a closure that logs a warning with some info about the missing translation key:

```php
use Spatie\Translatable\Facades\Translatable;
use Illuminate\Support\Facades\Log;

Translatable::fallback(missingKeyCallback: function (
   Model $model, 
   string $translationKey, 
   string $locale, 
   string $fallbackTranslation, 
   string $fallbackLocale,
) {

    // do something (ex: logging, alerting, etc)
    
    Log::warning('Some translation key is missing from an eloquent model', [
       'key' => $translationKey,
       'locale' => $locale,
       'fallback_locale' => $fallbackLocale,
       'fallback_translation' => $fallbackTranslation,
       'model_id' => $model->id,
       'model_class' => get_class($model), 
    ]);
});
```

If the closure returns a string, it will be used as the fallback translation:

```php
use Spatie\Translatable\Facades\Translatable;
use App\Service\MyRemoteTranslationService;

Translatable::fallback(missingKeyCallback: function (
    Model $model, 
    string $translationKey, 
    string $locale, 
    string $fallbackTranslation, 
    string $fallbackLocale
    ) {
    
    return MyRemoteTranslationService::getAutomaticTranslation($fallbackTranslation, $fallbackLocale, $locale);
});
```

### Disabling fallbacks on a per model basis
By default, a fallback will be used when you access a non-existent translation attribute.

You can disable fallbacks on a model with the `$useFallbackLocale` property.

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    
    protected $useFallbackLocale = false;
}
```
