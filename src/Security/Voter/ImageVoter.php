<?php

declare(strict_types=1);

namespace App\Security\Voter;


use Symfony\Component\HttpFoundation\Request;

class ImageVoter implements VoterInterface
{
    public function vote(Request $request): int
    {
        $uri = $request->getPathInfo();

        if ($uri === '/images/4711/delete') {
            if (true /* user is not "bob" */) {
                return VoterInterface::ACCESS_DENIED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
