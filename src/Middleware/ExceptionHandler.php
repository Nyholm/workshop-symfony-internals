<?php

namespace App\Middleware;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExceptionHandler implements MiddlewareInterface
{
    private $env;

    public function __construct(string $environment)
    {
        $this->env = $environment;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        try {
            $response = $next($request, $response);
        } catch (\Throwable $exception) {
            if ($this->env === 'dev') {
                $response = new Response(500, [], $exception->getMessage());
            } else {
                $response = new Response(500, [], 'Im sorry, try again later.');
            }
        }

        return $response;
    }
}
