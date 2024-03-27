<?php

namespace Vormkracht10\LaravelStatic\Crawler;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\View\Components\Factory as ComponentFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class StaticCrawlObserver extends CrawlObserver
{
    protected ComponentFactory $components;

    public function __construct(ComponentFactory $components)
    {
        $this->components = $components;
    }

    /**
     * Called when the crawler will crawl the url.
     */
    public function willCrawl(UriInterface $url, ?string $linkText): void
    {
        //
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        UriInterface $foundOnUrl = null,
        ?string $linkText
    ): void {
        $this->components->info('Crawled and cached url: '.$url);
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        UriInterface $foundOnUrl = null,
        ?string $linkText
    ): void {
        $this->components->error('Failed to crawl url: '.$url);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        $this->components->info('Static cache build completed');
    }
}
