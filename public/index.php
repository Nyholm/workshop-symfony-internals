<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;

require __DIR__.'/../vendor/autoload.php';

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
$request = $creator->fromGlobals();
$response = new Response();

$middlewares[] = new \App\Middleware\Router();

$runner = (new \Relay\RelayBuilder())->newInstance($middlewares);
$response = $runner($request, $response);

// Send response
echo $response->getBody();
