<?php

namespace Vormkracht10\LaravelStatic\Commands;

use Illuminate\Console\Command;
use Vormkracht10\LaravelStatic\LaravelStatic;

class StaticClearCommand extends Command
{
    public $signature = 'static:clear';

    public $description = 'Clear static cached files';

    public function handle(LaravelStatic $static): void
    {
        $static->clear();
    }
}
