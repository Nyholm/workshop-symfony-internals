<?php

namespace App\Middleware;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cache implements MiddlewareInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var int
     */
    private $ttl;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
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
