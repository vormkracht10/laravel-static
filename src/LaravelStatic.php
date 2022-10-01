<?php

namespace Vormkracht10\LaravelStatic;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class LaravelStatic
{
    protected Config $config;

    protected Filesystem $files;

    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
    }

    public function clear(): bool
    {
        $files = $this->files->allFiles(directory: $this->config->get('static.path'), hidden: false);

        return $this->files->delete($files);
    }

    public function forget(string $path): bool
    {
        return $this->files->delete($path);
    }
}
