<?php

namespace Spatie\Translatable;

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
        $this->app->singleton(Translatable::class, fn () => new Translatable());
        $this->app->bind('translatable', Translatable::class);
    }
}
