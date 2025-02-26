<?php

namespace Backstage\Laravel\Static\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Backstage\Laravel\Static\StaticServiceProvider;

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
