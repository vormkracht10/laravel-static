<?php

namespace Vormkracht10\LaravelStatic;

use Illuminate\Contracts\Http\Kernel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelStatic\Commands\StaticBuildCommand;
use Vormkracht10\LaravelStatic\Commands\StaticClearCommand;
use Vormkracht10\LaravelStatic\Middleware\PreventStaticResponseMiddleware;

class LaravelStaticServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-static')
            ->hasConfigFile()
            ->hasCommands([
                StaticClearCommand::class,
                StaticBuildCommand::class,
            ]);
    }

    public function packageBooted()
    {
        $kernel = $this->app->make(Kernel::class);

        $kernel->prependMiddlewareToGroup('web',
            PreventStaticResponseMiddleware::class
        );
    }
}
