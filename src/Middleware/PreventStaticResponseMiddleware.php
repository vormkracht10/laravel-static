<?php

namespace Backstage\Laravel\Static\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PreventStaticResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $bypass = config('static.build.bypass_header');

        $key = array_key_first($bypass);

        $value = $bypass[$key] ?? null;

        if (! $value || $request->header($key) !== $value) {
            return $next($request);
        }

        $route = Route::current();

        $hasStaticResponseMiddleware = in_array(
            StaticResponse::class,
            Route::gatherRouteMiddleware($route),
        );

        if (! $hasStaticResponseMiddleware) {
            return response(status: 200);
        }

        return $next($request);
    }
}
