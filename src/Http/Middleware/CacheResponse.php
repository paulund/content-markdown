<?php

namespace Paulund\ContentMarkdown\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    public function handle(Request $request, Closure $next, ...$args): Response
    {
        if ($request->getMethod() !== 'GET') {
            return $next($request);
        }

        if (! empty($request->session()->all())) {
            return $next($request);
        }

        $cacheEnabled = config('content-markdown.cache.enabled', true);
        $cacheKey = $this->getCacheKey($request);

        if ($cacheEnabled && Cache::store($this->cacheStore())->has($cacheKey)) {
            Log::debug('Cache hit for response', ['cache_key' => $cacheKey]);
            $cachedContent = Cache::store($this->cacheStore())->get($cacheKey);

            return new Response($cachedContent, 200, ['Content-Type' => 'text/html']);
        }

        $response = $next($request);

        if ($cacheEnabled && $response->getStatusCode() === 200) {
            Log::debug('Cache put for response', ['cache_key' => $cacheKey]);
            Cache::store($this->cacheStore())->put($cacheKey, $response->getContent(), config('content-markdown.cache.ttl', 3600));
            $response->header('Cache-Control', 'max-age=3600');
        }

        return $response;
    }

    protected function getCacheKey(Request $request): string
    {
        return 'response_cache_'.md5($request->getPathInfo());
    }

    private function cacheStore()
    {
        return app()->environment('testing') ? 'array' : config('content-markdown.cache.store', 'file');
    }
}
