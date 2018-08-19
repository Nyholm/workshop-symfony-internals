<?php

namespace App\Middleware;

use App\Security\Voter\VoterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SecurityVoters implements EventSubscriberInterface
{
    /** @var VoterInterface[] */
    private $voters;

    public function __construct(iterable $voters)
    {
        $this->voters = $voters;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $deny = 0;
        foreach ($this->voters as $voter) {
            $result = $voter->vote($request);
            switch ($result) {
                case VoterInterface::ACCESS_GRANTED:
                    return;

                case VoterInterface::ACCESS_DENIED:
                    ++$deny;

                    break;
                default:
                    break;
            }
        }

        if ($deny > 0) {
            $event->setResponse(new Response('Forbidden', 403));
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return ['kernel.request' => ['onRequest', 90]];
    }
}
