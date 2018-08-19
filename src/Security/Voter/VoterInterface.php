<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Psr\Http\Message\ServerRequestInterface;

interface VoterInterface
{
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    public function vote(ServerRequestInterface $request);
}
