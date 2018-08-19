<?php

namespace App\Middleware;

use App\Event\FilterResponseEvent;
use App\Event\GetResponseEvent;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            $event->setResponse(new Response($cacheItem->get()));

            // Interrupt the middleware chain
            $event->stopPropagation();
        }
    }

    public function onResponse(FilterResponseEvent $event)
    {
        $cacheItem = $this->getCacheItem($event->getRequest());
        $response = $event->getResponse();

        // Save the response in cache
        $cacheItem
            ->set($response->getContent())
            ->expiresAfter($this->ttl);
        $this->cache->save($cacheItem);

        return $response;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onRequest'],
            'kernel.response' => ['onResponse', -10],
        ];
    }

    private function getCacheItem(Request $request): \Psr\Cache\CacheItemInterface
    {
        $uri = $request->getPathInfo().$request->getQueryString();
        $cacheKey = 'url'.sha1($uri);
        $cacheItem = $this->cache->getItem($cacheKey);

        return $cacheItem;
    }
}
