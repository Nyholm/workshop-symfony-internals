<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $uri = $request->getUri()->getPath();

        switch ($uri) {
            case '/':
                $response = (new \App\Controller\StartpageController())->run($request);
                break;
            case '/foo':
                $response = (new \App\Controller\FooController())->run($request);
                break;
            default:
                $response = $response->withStatus(404);
                $response->getBody()->write('Not Found');
                break;
        }

        return $next($request, $response);
    }
}
