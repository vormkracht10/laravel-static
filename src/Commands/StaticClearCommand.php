<?php

namespace Vormkracht10\LaravelStatic\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Vormkracht10\LaravelStatic\LaravelStatic;

class StaticClearCommand extends Command
{
    public $signature = 'static:clear';

    public $description = 'Clear static cached files';

    protected Repository $config;

    protected LaravelStatic $static;

    public function __construct(Repository $config, LaravelStatic $static)
    {
        parent::__construct();

        $this->config = $config;
        $this->static = $static;
    }

    public function handle(): void
    {
        $this->static->clear();

        $this->info('✔ Static cache cleared!');
    }
}
