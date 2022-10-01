<?php

namespace Vormkracht10\LaravelStatic;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelStatic\Commands\StaticClearCommand;

class LaravelStaticServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-static')
            ->hasConfigFile()
            ->hasCommand(StaticClearCommand::class);
    }
}
