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

            if (count($route->parameterNames()) !== 0) {
                $id = $route->getName() ?? $route->uri();

                $this->components->warn("Skipping route [{$id}], can not build routes with parameters");

                continue;
            }

            if (! $response->isOk()) {
                $this->components->error("✘ failed to cache page on route \"{$route->uri()}\"");

                $failed++;

                continue;
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

        $crawler = Crawler::create([
            RequestOptions::VERIFY => ! app()->environment('local', 'testing'),
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::HEADERS => [
                array_key_first($bypassHeader) => array_shift($bypassHeader),
                'User-Agent' => 'LaravelStatic/1.0',
            ],
        ])
            ->acceptNofollowLinks()
            ->setCrawlObserver(new StaticCrawlObserver($this->components))
            ->setCrawlProfile($profile)
            ->setConcurrency($this->config->get('static.build.concurrency'))
            ->setDefaultScheme($this->config->get('static.build.default_scheme'));
        //            ->setParseableMimeTypes(['text/html', 'text/plain'])

        if ($this->config->get('static.build.accept_no_follow')) {
            $crawler->acceptNofollowLinks();
        }

        $crawler->startCrawling($this->config->get('app.url'));
    }
}
