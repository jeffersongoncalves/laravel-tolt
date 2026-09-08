<?php

namespace Jeffersongoncalves\Tolt;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ToltServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-tolt')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
