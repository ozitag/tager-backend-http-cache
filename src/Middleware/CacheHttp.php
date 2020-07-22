<?php

namespace OZiTAG\Tager\Backend\HttpCache\Middleware;

use Closure;
use OZiTAG\Tager\Backend\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Request;

class CacheHttp
{
    /**
     * The cache instance.
     *
     * @var HttpCache
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @var HttpCache  $cache
     */
    public function __construct(HttpCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->cache->shouldCache($request, $response)) {
            $this->cache->cacheRequest($request, $response);
        }

        return $response;
    }
}
