<?php

namespace App\Middleware;

use App\DataCollector\CacheDataCollector;
use App\Event\FilterResponseEvent;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Factory\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

        $content = $response->getBody()->__toString();
        $toolbar = <<<HTML
<br><br><br><hr>
URL: {$request->getUri()->getPath()}<br> 
IP: {$request->getServerParams()['REMOTE_ADDR']}<br> 
Cache calls: {$getItemCalls}<br>
HTML;

        $stream = (new Psr17Factory())->createStream($content.$toolbar);
        $response = $response->withBody($stream);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.response' => ['onResponse', -110]];
    }
}
