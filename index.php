<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;

require __DIR__.'/vendor/autoload.php';

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
$request = $creator->fromGlobals();

$query = $request->getQueryParams();
if (isset($query['page']) && $query['page'] === 'foo') {
    $response = new Response(200, [], 'Foo page');
} else {
    $response = new Response(200, [], 'Welcome to index!');
}

// Send response
echo $response->getBody();
