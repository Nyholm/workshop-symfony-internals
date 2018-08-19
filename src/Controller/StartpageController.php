<?php

declare(strict_types=1);

namespace App\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class StartpageController
{
    public function run(RequestInterface $request)
    {
        return new Response(200, [], 'Welcome to index!');
    }
}