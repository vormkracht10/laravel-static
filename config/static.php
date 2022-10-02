<?php

return [
    /**
     * Path within storage disk to save files in.
     */
    'path' => storage_path('app/static'),

    /**
     * Configure a fallback cache driver.
     */
    'fallback_cache' => 'memcached',

    /**
     * Clear static files before warming up static cache.
     * When disabled, the cache is warmed up rather by updating and overwriting files instead of starting without an existing cache.
     */
    'clear_before_warm_up' => false,

    /**
     * HTTP Header that is being sent to web server by warm up command, to recognize and pass through static cache and
     * hit the Laravel application on the server.
     */
    'warm_up_http_header' => 'X-Laravel-Static',

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
     * This setting prevents saving the same static cache file twice (with and without trailing slash) using a 302 redirect.
     * Enable when you want to use trailing slashes.
     */
    'use_trailing_slash' => false,

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
