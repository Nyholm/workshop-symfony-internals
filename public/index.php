<?php

use \Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

$request = Request::createFromGlobals();

$kernel = new \App\Kernel('dev', true);
$response = $kernel->handle($request);

// Send response
$response->send();