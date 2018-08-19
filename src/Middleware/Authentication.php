<?php

namespace App\Middleware;

use App\Event\GetResponseEvent;
use App\Security\TokenStorage;
use Nyholm\Psr7\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
        $uri = $request->getUri()->getPath();
        $auth = $request->getServerParams()['PHP_AUTH_USER'] ?? '';
        $pass = $request->getServerParams()['PHP_AUTH_PW'] ?? '';

        if ($uri !== '/admin') {
            return;
        }

        if (empty($auth)) {
            $event->setResponse(new Response(401, ['WWW-Authenticate' => 'Basic realm="Admin area"'], 'This page is protected'));

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
