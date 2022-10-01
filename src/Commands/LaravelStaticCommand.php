<?php

namespace Vormkracht10\LaravelStatic\Commands;

use Illuminate\Console\Command;

class LaravelStaticCommand extends Command
{
    public $signature = 'laravel-static';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
