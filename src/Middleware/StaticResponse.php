<?php

namespace Vormkracht10\LaravelStatic\Middleware;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use voku\helper\HtmlMin;
use Vormkracht10\LaravelStatic\Facades\LaravelStatic;

class StaticResponse
{
    protected Repository $config;

    protected array $bypassHeader;

    public function __construct(Repository $config)
    {
        $this->config = $config;

        $this->bypassHeader = $this->config->get('static.build.bypass_header');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if (
            ! $this->config->get('static.optimizations.on_termination') &&
            $this->shouldBeStatic($request, $response)
        ) {
            $response = $this->minifyResponse($response);

            $this->createStaticFile($request, $response);
        }

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, $response): void
    {
        if (
            $this->config->get('static.optimizations.on_termination') &&
            $this->shouldBeStatic($request, $response)
        ) {
            $response = $this->minifyResponse($response);

            $this->createStaticFile($request, $response);
        }
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    protected function shouldBeStatic(Request $request, $response): bool
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
     * Minify response.
     */
    public function minifyResponse($response)
    {
        if (! $this->config->get('static.minify_html')) {
            return $response;
        }

        if (! str_starts_with($response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $response->setContent(
            (new HtmlMin())
                ->minify($response->getContent())
        );

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function createStaticFile(Request $request, $response): void
    {
        [$path, $file] = $this->generateFilepath($request, $response);

        $filepath = $this->joinPaths([
            $request->getHost(),
            $request->method(),
            $path,
            $file,
        ]);

        if ($this->exceedsMaxLength($filepath)) {
            return;
        }

        $disk = LaravelStatic::disk();

        $disk->makeDirectory($path);

        if (! $disk->exists($this->config->get('static.path').'/.gitignore')) {
            $disk->put($this->config->get('static.path').'/.gitignore', '*'.PHP_EOL.'!.gitignore');
        }

        if ($response->getContent()) {
            $disk->put($filepath, $response->getContent(), true);
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
    public function getDomain(Request $request): ?string
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
    protected function getFileExtension($filename, $response): ?string
    {
        $contentType = $response->headers->get('Content-Type');

        $extension = 'html';

        if (
            $response instanceof JsonResponse ||
            $contentType == 'application/json'
        ) {
            $extension = 'json';
        }

        if (
            str_starts_with($contentType, 'text/xml') ||
            str_starts_with($contentType, 'application/xml')
        ) {
            $extension = 'xml';
        }

        if (str_ends_with($filename, $extension)) {
            return null;
        }

        return '.'.$extension;
    }

    /**
     * Generate static file path based on request following a matching pattern configured in Nginx
     */
    public function generateFilepath(Request $request, $response): array
    {
        $parts = $this->getUriParts($request);

        $filename = '';

        if (! str_ends_with($request->getPathInfo(), '/')) {
            $filename = array_pop($parts);
        }

        $path = $this->joinPaths([
            $this->basePath($request),
            $parts,
        ]);

        $filename .= '?';

        if (
            $this->config->get('static.include_query_string') &&
            ! blank($request->server('QUERY_STRING'))
        ) {
            $filename .= $request->server('QUERY_STRING');
        }

        $filename .= $this->getFileExtension($filename, $response);

        return [$path, $filename];
    }

    /**
     * Check maximum filepath and filename length.
     */
    public function exceedsMaxLength(string $filepath): bool
    {
        $filenameLength = strlen(basename($filepath));

        if ($filenameLength >= $this->config->get('static.files.filename_max_length')) {
            return true;
        }

        $filepathLength = strlen($filepath);

        if ($filepathLength >= $this->config->get('static.files.filepath_max_length')) {
            return true;
        }

        return false;
    }
}
