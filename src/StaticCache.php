<?php

namespace Backstage\Laravel\Static;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem as Files;
use Illuminate\Filesystem\FilesystemManager as Storage;

class StaticCache
{
    public function __construct(
        protected Repository $config,
        protected Files $files,
        protected Storage $storage,
    ) {}

    public function clear(?array $paths = null): bool
    {
        if (! is_null($paths)) {
            return $this->disk()->delete($paths);
        }

        return $this->files->cleanDirectory($this->disk()->getConfig()['root']);
    }

    public function disk(?string $override = null)
    {
        return $this->storage->disk($override ?? $this->config->get('static.files.disk'));
    }
}
