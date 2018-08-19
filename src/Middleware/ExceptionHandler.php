<?php

namespace App\Middleware;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionHandler implements EventSubscriberInterface
{
    private $env;

    public function __construct(string $environment)
    {
        $this->env = $environment;
    }

    public function onException(GetResponseForExceptionEvent $event)
    {
        if ($event->hasResponse()) {
            return;
        }

        if ($this->env === 'dev') {
            $response = new Response($event->getException()->getMessage(), 500);
        } else {
            $response = new Response('Im sorry, try again later.', 500);
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.exception' => ['onException', -10]];
    }
}
