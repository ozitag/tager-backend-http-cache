<?php

namespace OZiTAG\Tager\Backend\HttpCache\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;

class DoNotCacheHttp
{

    /**
     * Handle an incoming request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->add(['http-cache.disable' => true]);
        return $next($request);
    }
}
