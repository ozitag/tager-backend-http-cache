<?php

namespace OZiTAG\Tager\Backend\HttpCache;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpCache
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container|null
     */
    protected $container = null;


    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Sets the container instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(\Illuminate\Contracts\Container\Container $container)
    {
        $this->container = $container;

        return $this;
    }

    private function checkFolder($folderPath)
    {
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755);
        }

        return $folderPath;
    }

    private function getDataFolder()
    {
        $folder = storage_path('app/public/http-cache');

        $this->checkFolder($folder);

        return $folder;
    }

    /**
     * Get the names of the directory and file.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return array
     * @throws Exception
     */
    protected function getDirectoryAndFileNames($request, $response)
    {
        $segments = explode('/', ltrim($request->getPathInfo(), '/'));

        $filename = $this->aliasFilename(array_pop($segments));
        $extension = $this->guessFileExtension($response);

        if (!empty($request->getQueryString())) {
            $filename .= '?' . $request->getQueryString();
        }

        $file = "{$filename}.{$extension}";

        return [$this->getCachePath(implode('/', $segments)), $file];
    }


    /**
     * Gets the path to the cache directory.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getCachePath()
    {
        $base = $this->getDataFolder();

        if (is_null($base)) {
            throw new Exception('Cache path not set.');
        }

        return $this->join(array_merge([$base], func_get_args()));
    }

    /**
     * Makes the target path absolute if the source path is also absolute.
     *
     * @param string $source
     * @param string $target
     * @return string
     */
    protected function matchRelativity($source, $target)
    {
        return $source[0] == '/' ? '/' . $target : $target;
    }

    /**
     * Join the given paths together by the system's separator.
     *
     * @param string[] $paths
     * @return string
     */
    protected function join(array $paths)
    {
        $trimmed = array_map(function ($path) {
            return trim($path, '/');
        }, $paths);

        return $this->matchRelativity(
            $paths[0], implode('/', array_filter($trimmed))
        );
    }

    /**
     * Alias the filename if necessary.
     *
     * @param string $filename
     * @return string
     */
    protected function aliasFilename(?string $filename): string
    {
        return $filename ?: 'pc__index__pc';
    }

    /**
     * Guess the correct file extension for the given response.
     *
     * Currently, only JSON and HTML are supported.
     *
     * @param Response $response
     * @return string
     */
    protected function guessFileExtension(Response $response): string
    {
        return $response->headers->get('Content-Type') === 'application/json' ? 'json' : 'html';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws Exception
     */
    public function cacheRequest(Request $request, Response $response): void
    {
        try {
            list($path, $file) = $this->getDirectoryAndFileNames($request, $response);

            $this->checkFolder($path);

            $f = fopen($path . '/' . $file, 'w+');
            fwrite($f, $response->getContent());
            fclose($f);
        } catch (Exception $ex) {
        }
    }


    /**
     * Determines whether the given request/response pair should be cached.
     *
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function shouldCache(Request $request, Response $response): bool
    {
        if (!config('tager-http-cache.enabled')) {
            return false;
        }

        if ($request->attributes->has('http-cache.disable')) {
            return false;
        }

        if ($request->getMethod() !== 'GET') {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return true;
    }

    public function clear($namespace)
    {
        $cacheFolder = $this->getDataFolder();

        $this->filesystem->delete($cacheFolder . '/' . $namespace . '.json');
        $this->filesystem->deleteDirectory($cacheFolder . '/' . $namespace);
    }
}
