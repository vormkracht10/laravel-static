<?php

return [
    /**
     * Path within storage disk to save files in.
     */
    'path' => storage_path('static'),

    /**
     * Configure a fallback cache driver.
     */
    'fallback_cache' => 'memcached',

    /**
     * Different caches per domain.
     */
    'include_domain' => true,

    /**
     * When query string is included, every unique query string creates a new static file.
     */
    'include_query_string' => true,

    /**
     * Set path maximum length (determined by operating system config)
     */
    'filepath_max_length' => 4096,

    /**
     * Filename maximum length (determined by operating system config)
     */
    'filename_max_length' => 255,

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
    'minify' => true,

    /**
     * Clear static files before warming up static cache.
     */
    'clear_before_warm_up' => false,
];
