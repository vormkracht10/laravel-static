<?php

namespace Backstage\Static\Laravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Backstage\Static\Laravel\StaticServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            StaticServiceProvider::class,
        ];
    }
}
