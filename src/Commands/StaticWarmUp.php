<?php

namespace Vormkracht10\LaravelStatic\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Vormkracht10\LaravelStatic\LaravelStatic;

class StaticClearCommand extends Command
{
    public $signature = 'static:clear';

    public $description = 'Clear static cached files';

    public function handle(Config $config, LaravelStatic $static): void
    {
        if ($config->get('clear_before_warm_up')) {
            $static->clear();
        }
    }
}
