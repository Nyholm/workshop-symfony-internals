<?php

declare(strict_types=1);

namespace App\Security\Voter;


use Symfony\Component\HttpFoundation\Request;

interface VoterInterface
{
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    public function vote(Request $request): int;
}
