<?php

declare(strict_types=1);

namespace App\Event;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * First event dispatched. We are looking for a response now.
 */
class GetResponseForExceptionEvent extends GetResponseEvent
{
    private $exception;

    public function __construct(ServerRequestInterface $request, \Throwable $exception)
    {
        parent::__construct($request);
        $this->exception = $exception;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
