<?php

namespace Vormkracht10\LaravelStatic\Middleware;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use voku\helper\HtmlMin;

class StaticResponse
{
    protected Repository $config;

    protected Filesystem $files;

    protected array $bypassHeader;

    public function __construct(Repository $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files = $files;
        $this->bypassHeader = $this->config->get('static.build.bypass_header');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (null !== $redirect = $this->shouldChangeTrailingSlash($request)) {
            return redirect($redirect);
        }

        $response = $next($request);

        if (
            ! $this->config->get('static.on_termination') &&
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
    public function terminate(Request $request, Response $response): void
    {
        if (
            $this->config->get('static.on_termination') &&
            $this->shouldBeStatic($request, $response)
        ) {
            $response = $this->minifyResponse($response);

            $this->createStaticFile($request, $response);
        }
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    protected function shouldChangeTrailingSlash(Request $request): string|null
    {
        $queryString = ($request->getQueryString() ? '?'.$request->getQueryString() : '');

        if (
            ! $request->is('/') &&
            $this->config->get('static.use_trailing_slash') &&
            ! str_ends_with($request->getPathInfo(), '/')
        ) {
            return $request->getSchemeAndHttpHost().$request->getPathInfo().'/'.$queryString;
        }

        if (
            ! $request->is('/') &&
            ! $this->config->get('static.use_trailing_slash') &&
            str_ends_with($request->getPathInfo(), '/')
        ) {
            return $request->getSchemeAndHttpHost().rtrim($request->getPathInfo(), '/').$queryString;
        }

        return null;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    protected function shouldBeStatic(Request $request, Response $response): bool
    {
        return
            $request->isMethod('GET') &&
            $response->getStatusCode() == 200 &&
            $request->header(array_key_first($this->bypassHeader)) != array_shift($this->bypassHeader);
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
    public function minifyResponse(Response $response): Response
    {
        if (! $this->config->get('static.minify_html')) {
            return $response;
        }

        if (! starts_with($response->headers->get('Content-Type'), 'text/html')) {
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
    public function createStaticFile(Request $request, Response $response): void
    {
        [$path, $file] = $this->generateFilepath($request, $response);

        $filepath = $this->joinPaths([
            $path,
            $file,
        ]);

        if ($this->exceedsMaxLength($filepath)) {
            return;
        }

        $this->files->makeDirectory($path, 0775, true, true);

        if (! $this->files->exists($this->config->get('static.path').'/.gitignore')) {
            $this->files->put($this->config->get('static.path').'/.gitignore', '*'.PHP_EOL.'!.gitignore');
        }

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

        $filename = '';

        if (! str_ends_with($request->getPathInfo(), '/')) {
            $filename = array_pop($parts);
        }

        $path = $this->joinPaths([
            $this->basePath($request),
            $parts,
        ]);

        if ($this->config->get('static.include_query_string')) {
            $filename .= '?'.$request->server('QUERY_STRING');
        }

        $filename .= '.'.$this->getFileExtension($response);

        return [$path, $filename];
    }

    /**
     * Check maximum filepath and filename length.
     */
    public function exceedsMaxLength(string $filepath): bool
    {
        $filenameLength = strlen(basename($filepath));

        if ($filenameLength >= $this->config->get('static.filename_max_length')) {
            return true;
        }

        $filepathLength = strlen($filepath);

        if ($filepathLength >= $this->config->get('static.filepath_max_length')) {
            return true;
        }

        return false;
    }
}
