<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Psr\Http\Message\ServerRequestInterface;

class ImageVoter implements VoterInterface
{
    public function vote(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();

        if ($uri === '/images/4711/delete') {
            if (true /* user is not "bob" */) {
                return VoterInterface::ACCESS_DENIED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
