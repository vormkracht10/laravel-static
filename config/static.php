<?php

return [
    /**
     * Path within storage disk to save files in.
     */
    'path' => storage_path('public/static'),

    /**
     * Configure a fallback cache driver.
     */
    'fallback_cache' => 'memcached',

    'build' => [
        /**
         * Clear static files before building static cache.
         * When disabled, the cache is warmed up rather by updating and overwriting files instead of starting without an existing cache.
         */
        'clear_before_start' => false,

        /**
         * Concurrency for crawling to warm up static cache.
         */
        'concurrency' => 5,

        /**
         * HTTP header that can be used to bypass the cache. Useful for updating the cache without needing to clear it first,
         * or to monitor the performance of your application.
         */
        'bypass_header' => [
            'X-Laravel-Static' => 'off',
        ],
    ],

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
     * Define if you want to save the static cache after response has been sent to browser.
     */
    'on_termination' => false,

    /**
     * Minify HTML before saving static file.
     */
    'minify_html' => true,

    /**
     * Set file path maximum length (determined by operating system config)
     */
    'filepath_max_length' => 4096,

    /**
     * Set filename maximum length (determined by operating system config)
     */
    'filename_max_length' => 255,
];
