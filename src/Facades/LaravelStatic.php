<?php

namespace Vormkracht10\LaravelStatic\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vormkracht10\LaravelStatic\LaravelStatic
 */
class LaravelStatic extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vormkracht10\LaravelStatic\LaravelStatic::class;
    }
}
