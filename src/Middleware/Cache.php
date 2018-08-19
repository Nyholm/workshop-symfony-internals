<?php

namespace App\Middleware;

use App\Event\FilterResponseEvent;
use App\Event\GetResponseEvent;
use Nyholm\Psr7\Response;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Cache implements EventSubscriberInterface
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

    public function onRequest(GetResponseEvent $event)
    {
        $cacheItem = $this->getCacheItem($event->getRequest());

        if ($cacheItem->isHit()) {
            $event->setResponse(new Response(200, [], $cacheItem->get()));

            // Interrupt the middleware chain
            $event->stopPropagation();
        }
    }

    public function onResponse(FilterResponseEvent $event)
    {
        $cacheItem = $this->getCacheItem($event->getRequest());
        if ($cacheItem->isHit()) {
            return;
        }

        $response = $event->getResponse();

        // Save the response in cache
        $cacheItem
            ->set($response->getBody()->__toString())
            ->expiresAfter($this->ttl);
        $this->cache->save($cacheItem);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onRequest'],
            'kernel.response' => ['onResponse', -10],
        ];
    }

    private function getCacheItem(ServerRequestInterface $request): \Psr\Cache\CacheItemInterface
    {
        $uri = $request->getUri();
        $cacheKey = 'url'.sha1($uri->getPath().'?'.$uri->getQuery());
        $cacheItem = $this->cache->getItem($cacheKey);

        return $cacheItem;
    }
}
