<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\TokenStorage;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class AdminController
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function run(RequestInterface $request)
    {
        return new Response(200, [], sprintf('Hello %s (admin)', $this->tokenStorage->getLastToken()['username']));
    }
}
