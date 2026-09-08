<?php

namespace JeffersonGoncalves\Tolt;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ToltServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('tolt')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Tolt::class);
    }
}
