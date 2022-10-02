<?php

namespace Vormkracht10\LaravelStatic\Crawler;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class StaticCrawlObserver extends CrawlObserver
{
    /**
     * Called when the crawler will crawl the url.
     *
     * @param  \Psr\Http\Message\UriInterface  $url
     */
    public function willCrawl(UriInterface $url): void
    {
        // ...
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param  \Psr\Http\Message\UriInterface  $url
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @param  \Psr\Http\Message\UriInterface|null  $foundOnUrl
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        console()->info('✔ '.$url);
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param  \Psr\Http\Message\UriInterface  $url
     * @param  \GuzzleHttp\Exception\RequestException  $requestException
     * @param  \Psr\Http\Message\UriInterface|null  $foundOnUrl
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
        console()->error('✘ '.$url);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        console()->info('✔ Static build completed');
    }
}
