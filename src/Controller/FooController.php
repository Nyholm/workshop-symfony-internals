<?php

declare(strict_types=1);

namespace App\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class FooController
{
    public function run(RequestInterface $request)
    {
        return new Response(200, [], 'Foo page');
    }
}