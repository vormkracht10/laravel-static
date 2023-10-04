<?php

return [
    /**
     * Path within storage disk to save files in.
     */
    'path' => storage_path('public/static'),

    'build' => [
        /**
         * Use a web crawler to find all links to cache
         */
        'crawler' => true,
    
        /**
         * Build cache for non-dynamic routes
         */
        'routes' => false,
    
        /**
         * Build cache for these defined URLs
         * Ideally when using dynamic routes, like /posts/{slug}
         */
        'urls' => [],
    
        /**
         * Clear static files before building static cache.
         * When disabled, the cache is warmed up rather by updating and overwriting files instead of starting without an existing cache.
         */
        'clear_before_start' => false,

        /**
         * Number of concurrent http requests to build static cache.
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

    'files' => [
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
