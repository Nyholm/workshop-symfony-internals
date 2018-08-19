<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;

require __DIR__.'/../vendor/autoload.php';

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
$request = $creator->fromGlobals();

$uri = $request->getUri()->getPath();
if ($uri === '/') {
    $response = (new \App\Controller\StartpageController())->run($request);
} elseif ($uri === '/foo') {
    $response = (new \App\Controller\FooController())->run($request);
} else {
    $response = new Response(404, [], 'Not found');
}

// Send response
echo $response->getBody();
