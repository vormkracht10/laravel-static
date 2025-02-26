<?php

namespace Backstage\Laravel\Static\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route as Router;
use Backstage\Laravel\Static\StaticCache;

class StaticClearCommand extends Command
{
    public $signature = 'static:clear {--u|uri=*} {--r|routes=*} {--d|domain=*}';

    public $description = 'Clear static cached files';

    public function __construct(protected Repository $config, protected Static $static)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $uris = $this->option('uri');

        if (! empty($uris)) {
            $paths = $this->preparePaths($uris);

            $this->static->clear($paths);
        } elseif ($routes = Arr::wrap(
            $this->option('routes'),
        )) {
            $this->purgeWithRoutes($routes);
        } else {
            $this->static->clear();
        }

        $this->info('✔ Static cache cleared!');
    }

    protected function purgeWithRoutes(array $names)
    {
        $routes = Router::getRoutes();

        foreach ($names as $name) {
            $route = $routes->getByName($name);

            if (is_null($route)) {
                $this->components->warn('Route '.$name.' not found');

                continue;
            }

            if (count($route->parameterNames()) !== 0) {
                $this->components->warn('Route '.$name.' expects parameters, use the -u option instead');

                continue;
            }

            $this->purgeRoute($route);
        }
    }

    protected function purgeRoute(Route $route)
    {
        $path = $this->preparePaths(
            $route->uri(),
        );

        $this->static->clear($path);
    }

    protected function preparePaths($uris): array
    {
        $uris = Arr::wrap($uris);

        foreach ($uris as $uri) {
            $paths[] = ($this->option('domain')[0] ? $this->option('domain')[0].'/' : '').'GET/'.ltrim($uri, '/').'?.html';
        }

        return $paths ?? [];
    }
}
