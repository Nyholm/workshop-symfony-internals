<?php

namespace App\Middleware;

use App\DataCollector\CacheDataCollector;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Factory\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Toolbar implements MiddlewareInterface
{
    private $cacheDataCollector;

    public function __construct(CacheDataCollector $cacheDataCollector)
    {
        $this->cacheDataCollector = $cacheDataCollector;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
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

        return $next($request, $response);
    }
}
