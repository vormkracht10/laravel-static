<?php

namespace Backstage\Laravel\Static\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Vormkracht10\LaravelStatic\LaravelStatic
 */
class StaticCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\Laravel\Static\StaticCache::class;
    }
}
