<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\HttpFoundation\Request;

/**
 * First event dispatched. We are looking for a response now.
 */
class GetResponseForExceptionEvent extends GetResponseEvent
{
    private $exception;

    public function __construct(Request $request, \Throwable $exception)
    {
        parent::__construct($request);
        $this->exception = $exception;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
