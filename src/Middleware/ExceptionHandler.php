<?php

namespace App\Middleware;

use App\Event\GetResponseForExceptionEvent;
use Nyholm\Psr7\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            $response = new Response(500, [], $event->getException()->getMessage());
        } else {
            $response = new Response(500, [], 'Im sorry, try again later.');
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.exception' => ['onException', -10]];
    }
}
