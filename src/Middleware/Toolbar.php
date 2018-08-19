<?php

namespace App\Middleware;

use App\DataCollector\CacheDataCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Toolbar implements EventSubscriberInterface
{
    private $cacheDataCollector;

    public function __construct(CacheDataCollector $cacheDataCollector)
    {
        $this->cacheDataCollector = $cacheDataCollector;
    }

    public function onResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $calls = $this->cacheDataCollector->getCalls();
        $getItemCalls = count($calls['getItem']);

        $content = $response->getContent();
        $toolbar = <<<HTML
<br><br><br><hr>
URL: {$request->getPathInfo()}<br> 
IP: {$request->server->get('REMOTE_ADDR')}<br> 
Cache calls: {$getItemCalls}<br>
HTML;

        $response = $response->setContent($content.$toolbar);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.response' => ['onResponse', -110]];
    }
}
