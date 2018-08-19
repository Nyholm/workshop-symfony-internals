<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Container;

class Router implements MiddlewareInterface
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $uri = $request->getUri()->getPath();

        switch ($uri) {
            case '/':
                $response = $this->container->get('controller.startpage')->run($request);
                break;
            case '/foo':
                $response = $this->container->get('controller.foo')->run($request);
                break;
            case '/admin':
                $response = $this->container->get('controller.admin')->run($request);
                break;
            default:
                $response = $response->withStatus(404);
                $response->getBody()->write('Not Found');
                break;
        }

        return $next($request, $response);
    }
}
