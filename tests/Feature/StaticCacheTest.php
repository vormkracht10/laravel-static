<?php

use Illuminate\Support\Facades\Route;
use voku\helper\HtmlMin;
use Backstage\Static\Laravel\Facades\StaticCache;
use Backstage\Static\Laravel\Middleware\StaticResponse;

it('can cache a page response', function ($route) {
    config([
        'static.files.disk' => 'local',
    ]);

    $disk = StaticCache::disk();

    Route::get($route, fn () => $route)
        ->middleware(StaticResponse::class);

    $this->get($route);

    $path = "localhost/GET/{$route}?.html";

    $disk->assertExists($path);

    $content = $disk->get($path);

    expect($content)
        ->toBeString()
        ->toBe($route);
})->with(['hello', '1289bwa jk912UIwa', '*!@)(!', '123=']);

it('minifies HTML', function () {
    config([
        'static.files.disk' => 'local',
        'static.options.minify_html' => true,
    ]);

    $disk = StaticCache::disk();

    $html = <<<'HTML'
<h1>Hello!</h1>
<h2>Hello</h2>
HTML;

    $minified = (new HtmlMin)->minify($html);

    Route::get('/', fn () => $html)
        ->middleware(StaticResponse::class);

    $this->get('/');

    $actual = $disk->get('localhost/GET/?.html');

    expect($actual)
        ->toBeString()
        ->toBe($minified);
});
