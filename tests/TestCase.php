<?php

namespace Vormkracht10\LaravelStatic\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vormkracht10\LaravelStatic\LaravelStaticServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelStaticServiceProvider::class,
        ];
    }
}
