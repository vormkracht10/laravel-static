<?php

namespace Vormkracht10\LaravelStatic\Middleware;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

class StaticResponse
{
    protected Config $config;

    protected Filesystem $files;

    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if (
            ! $this->config->get('static.on_termination') &&
            $this->shouldBeStatic($request, $response)
        ) {
            $this->createStaticFile($request, $response);
        }

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (
            $this->config->get('static.on_termination') &&
            $this->shouldBeStatic($request, $response)
        ) {
            $this->createStaticFile($request, $response);
        }
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    protected function shouldBeStatic(Request $request, Response $response): bool
    {
        return
            $request->isMethod('GET') &&
            $response->getStatusCode() == 200;
    }

    /**
     * Join an array of paths to a string
     */
    public function joinPaths(array $paths): string
    {
        return collect($paths)->map(function ($path) {
            if (is_array($path)) {
                return implode('/', $path);
            }

            return $path;
        })->implode('/');
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function createStaticFile(Request $request, Response $response): void
    {
        [$path, $file] = $this->generateFilepath($request, $response);

        $filepath = $this->joinPaths([
            $this->basePath($request),
            $path,
            $file,
        ]);

        if ($this->exceedsMaxLength($filepath)) {
            return;
        }

        $this->files->makeDirectory($path, 0775, true, true);

        if ($response->getContent()) {
            $this->files->put($filepath, $response->getContent(), true);
        }
    }

    /**
     * Get URI.
     */
    public function getUri(Request $request): string
    {
        return trim($request->getPathInfo(), '/') ?: '/';
    }

    /**
     * Get URI in parts.
     */
    public function getUriParts(Request $request): array
    {
        return array_filter(explode('/', $this->getUri($request)));
    }

    /**
     * Get URI in parts.
     */
    public function getDomain(Request $request): string|null
    {
        return $request->server('HTTP_HOST');
    }

    /**
     * Get base path for generating file path.
     */
    public function basePath(Request $request): string
    {
        $path = $this->config->get('static.path');
        $path = rtrim($path, '/');

        if ($this->config->get('static.include_domain')) {
            $path .= '/'.$this->getDomain($request);
        }

        return $path;
    }

    /**
     * Get file extension based on response content type.
     */
    protected function getFileExtension(Response|JsonResponse $response): string
    {
        $contentType = $response->headers->get('Content-Type');

        if (
            $response instanceof JsonResponse ||
            $contentType == 'application/json'
        ) {
            return 'json';
        }

        if (in_array($contentType, ['text/xml', 'application/xml'])) {
            return 'xml';
        }

        return 'html';
    }

    /**
     * Generate static file path based on request following a matching pattern configured in Nginx
     */
    public function generateFilepath(Request $request, Response $response): array
    {
        $parts = $this->getUriParts($request);

        $file = array_pop($parts);

        if ($this->config->get('static.include_query_string')) {
            $parts[] = '?'.$request->server('QUERY_STRING');
        }

        $path = $this->joinPaths([
            $this->basePath($request),
            $parts,
        ]);

        $filename = $file.'.'.$this->getFileExtension($response);

        return [$path, $filename];
    }

    /**
     * Check maximum filepath and filename length.
     */
    public function exceedsMaxLength(string $filepath): bool
    {
        $filenameLength = strlen(basename($filepath));

        if ($filenameLength >= $this->config->get('filename_max_length')) {
            return true;
        }

        $filepathLength = strlen($filepath);

        if ($filepathLength >= $this->config->get('filepath_max_length')) {
            return true;
        }

        return false;
    }
}
