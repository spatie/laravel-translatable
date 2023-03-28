<?php

namespace Spatie\Translatable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslatableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-translatable');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Translatable::class, fn () => new Translatable());
        $this->app->bind('translatable', Translatable::class);
        
        Factory::macro('translatable', function (string|array $locales, mixed $value) {
            return json_encode(
                is_array($value)
                    ? array_combine((array)$locales, $value)
                    : array_fill_keys((array)$locales, $value)
            );
        });
    }
}
