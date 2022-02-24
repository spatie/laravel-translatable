<?php

return [

    /*
     * If a translation has not been set for a given locale, use this locale instead.
     */
    'fallback_locale' => null,

    /*
     * If a translation has not been set for a given locale and the fallback locale,
     * any other locale will be chosen instead.
     */
    'fallback_any' => false,

    /*
     * If you want to execute a callback when a translation key is missing
     */
    'fallback_callback_enabled' => false,

    /*
     * Name of the class implementing `Spatie\Translatable\FallbackCallback` that is called
     * when a translation key is missing
     */
    'fallback_callback_class' => '',
];
