<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\TokenStorage;
use Psr\Http\Message\ServerRequestInterface;

class AdminVoter implements VoterInterface
{
    private $tokenStorage;

    public function __construct(TokenStorage$tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function vote(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        $token = $this->tokenStorage->getLastToken();

        if (null === $token) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (strtolower($token['username']) !== 'alice' && $uri === '/admin') {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
