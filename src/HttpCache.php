<?php

namespace OZiTAG\Tager\Backend\HttpCache;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpCache
{
    private function checkFolder($folderPath)
    {
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755);
        }

        return $folderPath;
    }

    private function getDataFolder()
    {
        $folder = realpath(__DIR__ . '/../../../../storage/app/tager-data');

        $this->checkFolder($folder);

        return $folder;
    }

    /**
     * Get the names of the directory and file.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return array
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
     * @param string ...$paths
     * @return string
     *
     * @throws \Exception
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
    protected function aliasFilename($filename)
    {
        return $filename ?: 'pc__index__pc';
    }

    /**
     * Guess the correct file extension for the given response.
     *
     * Currently, only JSON and HTML are supported.
     *
     * @return string
     */
    protected function guessFileExtension($response)
    {
        if ($response instanceof JsonResponse) {
            return 'json';
        }

        return 'html';
    }

    public function cacheRequest(Request $request, Response $response)
    {
        list($path, $file) = $this->getDirectoryAndFileNames($request, $response);

        $this->checkFolder($path);

        $f = fopen($path . '/' . $file, 'w+');
        fwrite($f, json_encode($response->getContent()));
        fclose($f);
    }
}
