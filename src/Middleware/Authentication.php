<?php

namespace App\Middleware;

use App\Event\GetResponseEvent;
use App\Security\TokenStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class Authentication implements EventSubscriberInterface
{
    private $tokenStorage;

    /**
     * @param $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getPathInfo();
        $auth = $request->server->get('PHP_AUTH_USER', '');
        $pass = $request->server->get('PHP_AUTH_PW', '');

        if ($uri !== '/admin') {
            return;
        }

        if (empty($auth)) {
            $event->setResponse(new Response('This page is protected', 401, ['WWW-Authenticate' => 'Basic realm="Admin area"']));

            return;
        }

        // TODO check if $auth and $pass is correct
        $token = sha1(random_bytes(100));
        $this->tokenStorage->addToken(['token' => $token, 'username' => $auth]);
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.request' => ['onRequest', 100]];
    }
}
