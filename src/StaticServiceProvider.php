<?php

namespace Backstage\Laravel\Static;

use Illuminate\Contracts\Http\Kernel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Backstage\Laravel\Static\Commands\StaticBuildCommand;
use Backstage\Laravel\Static\Commands\StaticClearCommand;
use Backstage\Laravel\Static\Middleware\PreventStaticResponseMiddleware;

class StaticServiceProvider extends PackageServiceProvider
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

        $kernel->prependMiddlewareToGroup(
            'web',
            PreventStaticResponseMiddleware::class
        );
    }
}
