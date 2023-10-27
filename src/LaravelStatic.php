<?php

namespace Vormkracht10\LaravelStatic;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class LaravelStatic
{
    protected Repository $config;

    protected Filesystem $files;

    public function __construct(Repository $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
    }

    public function clear(array $paths = null): bool
    {
        $disk = $this->disk();

        if (! is_null($paths)) {
            return $disk->delete($paths);
        }

        $files = $disk->allFiles();

        return $disk->delete($files);
    }

    public function forget(string $path): bool
    {
        return $this->files->delete($path);
    }

    public function disk(string $override = null): FilesystemContract
    {
        $disk = $override ?? $this->config->get(
            'static.files.disk',
        );

        return Storage::disk($disk);
    }
}
