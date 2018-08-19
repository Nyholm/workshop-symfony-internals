<?php

namespace App\Middleware;

use App\Controller\AdminController;
use App\Controller\ExceptionController;
use App\Controller\FooController;
use App\Controller\StartpageController;
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
                $response = $this->container->get(StartpageController::class)->run($request);
                break;
            case '/foo':
                $response = $this->container->get(FooController::class)->run($request);
                break;
            case '/admin':
                $response = $this->container->get(AdminController::class)->run($request);
                break;
            case '/exception':
                $response = $this->container->get(ExceptionController::class)->run($request);
                break;
            default:
                $response = $response->withStatus(404);
                $response->getBody()->write('Not Found');
                break;
        }

        return $next($request, $response);
    }
}
