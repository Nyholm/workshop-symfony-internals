<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class ExceptionController
{
    public function run(Request $request)
    {
        throw new \RuntimeException('This is an exception');
    }
}
