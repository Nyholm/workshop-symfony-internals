<?php

namespace App\Middleware;

use App\Security\TokenStorage;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface ;
use Psr\Http\Message\ServerRequestInterface;

class Authentication implements MiddlewareInterface
{
    private $tokenStorage;

    /**
     *
     * @param $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $uri = $request->getUri()->getPath();
        $auth = $request->getServerParams()['PHP_AUTH_USER']??'';
        $pass = $request->getServerParams()['PHP_AUTH_PW']??'';

        if ($uri !== '/admin') {
            return $next($request, $response);
        }

        if (empty($auth)) {
            return new Response(401, ['WWW-Authenticate'=>'Basic realm="Admin area"'], 'This page is protected');
        }

        // TODO check if $auth and $pass is correct
        $token = sha1(random_bytes(100));
        $this->tokenStorage->addToken(['token'=>$token, 'username'=>$auth]);

        return $next($request, $response);
    }
}
