<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\RequestInterface;

class ExceptionController
{
    public function run(RequestInterface $request)
    {
        throw new \RuntimeException('This is an exception');
    }
}
