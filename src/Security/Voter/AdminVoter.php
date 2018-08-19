<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Security\TokenStorage;
use Symfony\Component\HttpFoundation\Request;

class AdminVoter implements VoterInterface
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function vote(Request $request): int
    {
        $uri = $request->getPathInfo();
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
