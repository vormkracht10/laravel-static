<?php

namespace Backstage\Static\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vormkracht10\LaravelStatic\LaravelStatic
 */
class StaticCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\Static\Laravel\StaticCache::class;
    }
}
