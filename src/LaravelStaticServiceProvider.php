<?php

namespace Vormkracht10\LaravelStatic;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelStatic\Commands\LaravelStaticCommand;

class LaravelStaticServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-static')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-static_table')
            ->hasCommand(LaravelStaticCommand::class);
    }
}
