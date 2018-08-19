<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

require __DIR__.'/../vendor/autoload.php';

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
$request = $creator->fromGlobals();

$kernel = new \App\Kernel('dev', true);
$response = $kernel->handle($request);

// Send response
(new SapiEmitter())->emit($response);