<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FooController
{
    public function run(Request $request)
    {
        return new Response('Foo page');
    }
}
