<?php

return [
    /**
     * Path within storage disk to save files in.
     */
    'path' => storage_path('static'),

    /**
     * Different caches per domain.
     */
    'include_domain' => true,

    /**
     * When query string is included, every unique query string creates a new static file.
     */
    'include_query_string' => false,

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
    'on_termination' => true,

    /**
     * Clear static files before warming up static cache.
     */
    'clear_before_warm_up' => false,
];
