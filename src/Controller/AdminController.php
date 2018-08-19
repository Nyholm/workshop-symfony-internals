<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\TokenStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function run(Request $request)
    {
        return new Response(sprintf('Hello %s (admin)', $this->tokenStorage->getLastToken()['username']));
    }
}
