<?php

namespace Vormkracht10\LaravelStatic\Commands;

use Exception;
use GuzzleHttp\RequestOptions;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Spatie\Crawler\Crawler;
use Vormkracht10\LaravelStatic\Crawler\StaticCrawlObserver;
use Vormkracht10\LaravelStatic\LaravelStatic;
use Vormkracht10\LaravelStatic\Middleware\StaticResponse;

class StaticBuildCommand extends Command
{
    public $signature = 'static:build';

    public $description = 'Build Static version';

    protected Repository $config;

    protected LaravelStatic $static;

    public function __construct(Repository $config, LaravelStatic $static)
    {
        parent::__construct();

        $this->config = $config;
        $this->static = $static;
    }

    public function handle(): void
    {
        if ($this->config->get('static.build.clear_before_start', true)) {
            $this->call(StaticClearCommand::class);
        }

        match ($driver = $this->config->get('static.driver', 'routes')) {
            'crawler' => $this->cacheWithCrawler(),
            'routes' => $this->cacheWithRoutes(),
            default => throw new Exception("Static driver [{$driver}] is not supported"),
        };
    }

    public function cacheWithRoutes(): void
    {
        /**
         * @var Collection<\Illuminate\Routing\Route> $routes
         */
        $routes = collect(Route::getRoutes()->get('GET'))->filter(
            fn ($route) => in_array(StaticResponse::class, Route::gatherRouteMiddleware($route)),
        );

        $failed = 0;

        foreach ($routes as $route) {
            $request = Request::create($route->uri());

            $request->headers->set('User-Agent', 'LaravelStatic/1.0');

            $response = Route::dispatchToRoute($request);

            if (! $response->isOk()) {
                $this->components->error("✘ failed to cache page on route \"{$route->uri()}\"");
                $failed++;
            }

            $this->components->info("✔ page on route \"{$route->uri()}\" has been cached");
        }

        if ($failed > 0) {
            $this->components->warn("FAILED TO CACHE {$failed} PAGES");
        }
    }

    public function cacheWithCrawler(): void
    {
        $bypassHeader = $this->config->get('static.build.bypass_header');

        $profile = $this->config->get('static.build.crawl_profile');

        Crawler::create([
            RequestOptions::VERIFY => ! app()->environment('local', 'testing'),
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::HEADERS => [
                array_key_first($bypassHeader) => array_shift($bypassHeader),
                'User-Agent' => 'LaravelStatic/1.0',
            ],
        ])
            ->setCrawlObserver(new StaticCrawlObserver($this->components))
            ->setCrawlProfile($profile)
            ->acceptNofollowLinks()
            ->setConcurrency($this->config->get('static.build.concurrency'))
            ->setDefaultScheme('https')
//            ->setParseableMimeTypes(['text/html', 'text/plain'])
            ->startCrawling($this->config->get('app.url'));
    }
}
