<?php

use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Vormkracht10\LaravelStatic\Crawler\StaticCrawlObserver;

return [
    /**
     * The driver that will be used to cache your pages.
     * This can be either 'crawler' or 'routes'.
     */
    'driver' => 'crawler',

    'build' => [
        /**
         * Clear static files before building static cache.
         * When disabled, the cache is warmed up rather by updating and overwriting files instead of starting without an existing cache.
         */
        'clear_before_start' => true,

        /**
         * Number of concurrent http requests to build static cache.
         */
        'concurrency' => 5,

        /**
         * Whether to follow links on pages.
         */
        'accept_no_follow' => true,

        /**
         * The default scheme the crawler will use.
         */
        'default_scheme' => 'https',

        /**
         * The crawl observer that will be used to handle crawl related events.
         */
        'crawl_observer' => StaticCrawlObserver::class,

        /**
         * The crawl profile that will be used by the crawler.
         */
        'crawl_profile' => CrawlInternalUrls::class,

        /**
         * HTTP header that can be used to bypass the cache. Useful for updating the cache without needing to clear it first,
         * or to monitor the performance of your application.
         */
        'bypass_header' => [
            'X-Laravel-Static' => 'off',
        ],
    ],

    'files' => [
        /**
         * The filesystem disk that will be used to cache your pages.
         */
        'disk' => env('STATIC_FILESYSTEM_DISK', 'local'),

        /**
         * Different caches per domain.
         */
        'include_domain' => true,

        /**
         * When query string is included, every unique query string combination creates a new static file.
         * When disabled, the URL is marked as identical regardless of the query string.
         */
        'include_query_string' => true,

        /**
         * Set file path maximum length (determined by operating system config)
         */
        'filepath_max_length' => 4096,

        /**
         * Set filename maximum length (determined by operating system config)
         */
        'filename_max_length' => 255,
    ],

    'optimizations' => [
        /**
         * Define if you want to save the static cache after response has been sent to browser.
         */
        'on_termination' => false,

        /**
         * Minify HTML before saving static file.
         */
        'minify_html' => true,
    ],
];
