<?php

namespace Vormkracht10\LaravelStatic\Commands;

use GuzzleHttp\RequestOptions;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Vormkracht10\LaravelStatic\Crawler\StaticCrawlObserver;
use Vormkracht10\LaravelStatic\LaravelStatic;

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
        if ($this->config->get('static.build.clear_before_start')) {
            $this->call(StaticClearCommand::class);
        }

        $bypassHeader = $this->config->get('static.build.bypass_header');

        Crawler::create([
            RequestOptions::VERIFY => ! app()->environment('local'),
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::HEADERS => [
                array_key_first($bypassHeader) => array_shift($bypassHeader),
                'User-Agent' => 'LaravelStatic/1.0',
            ],
        ])
        ->setCrawlObserver(new StaticCrawlObserver)
        ->setCrawlProfile(new CrawlInternalUrls($this->config->get('app.url')))
        ->acceptNofollowLinks()
        ->setConcurrency($this->config->get('static.build.concurrency'))
        ->setDefaultScheme('https')
        // ->setParseableMimeTypes(['text/html', 'text/plain'])
        ->startCrawling($this->config->get('app.url'));
    }
}
