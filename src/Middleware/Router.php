<?php

namespace App\Middleware;

use App\Controller\AdminController;
use App\Controller\ExceptionController;
use App\Controller\FooController;
use App\Controller\StartpageController;
use App\Event\GetResponseEvent;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Router implements EventSubscriberInterface
{
    private $controllers;

    public function __construct(iterable $controllers)
    {
        foreach ($controllers as $controller) {
            $this->controllers[get_class($controller)] = $controller;
        }
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getUri()->getPath();

        switch ($uri) {
            case '/':
                $response = $this->controllers[StartpageController::class]->run($request);
                break;
            case '/foo':
                $response = $this->controllers[FooController::class]->run($request);
                break;
            case '/admin':
                $response = $this->controllers[AdminController::class]->run($request);
                break;
            case '/exception':
                $response = $this->controllers[ExceptionController::class]->run($request);
                break;
            default:
                $response = new Response(404);
                $response->getBody()->write('Not Found');
                break;
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.request' => ['onRequest', -10]];
    }
}
