<?php

namespace App\Middleware;

use App\Security\Voter\VoterInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SecurityVoters implements MiddlewareInterface
{
    /** @var  VoterInterface[] */
    private $voters;

    public function __construct(array $voters)
    {
        $this->voters = $voters;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $deny = 0;
        foreach ($this->voters as $voter) {
            $result = $voter->vote($request);
            switch ($result) {
                case VoterInterface::ACCESS_GRANTED:
                    return $next($request, $response);

                case VoterInterface::ACCESS_DENIED:
                    ++$deny;

                    break;
                default:
                    break;
            }
        }

        if ($deny > 0) {
            return new Response(403, [], 'Forbidden');
        }

        return $next($request, $response);
    }
}
