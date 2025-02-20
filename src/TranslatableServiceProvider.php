<?php

namespace Spatie\Translatable;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TranslatableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-translatable');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Translatable::class, fn () => new Translatable);
        $this->app->bind('translatable', Translatable::class);

        Factory::macro('translations', function (string|array $locales, mixed $value) {
            return is_array($value)
                ? array_combine((array) $locales, $value)
                : array_fill_keys((array) $locales, $value);
        });
    }
}
