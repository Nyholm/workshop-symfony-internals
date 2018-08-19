<?php

namespace App\Middleware;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cache implements MiddlewareInterface
{
    /*** @var CacheItemPoolInterface */
    private $cache;

    /** @var int */
    private $ttl;

    public function __construct()
    {
        $flysystem = new Filesystem(new Local(__DIR__.'/../../var/cache/fs'));
        $this->cache = new FilesystemCachePool($flysystem);
        $this->ttl = 300;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $uri = $request->getUri();
        $cacheKey = 'url'.sha1($uri->getPath().'?'.$uri->getQuery());
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $response->getBody()->write($cacheItem->get());

            // Interrupt the middleware chain
            return $response;
        }

        // Continue the chain of middlewares and get a response
        $response = $next($request, $response);

        // Save the response in cache
        $cacheItem
            ->set($response->getBody()->__toString())
            ->expiresAfter($this->ttl);
        $this->cache->save($cacheItem);

        return $response;
    }
}
