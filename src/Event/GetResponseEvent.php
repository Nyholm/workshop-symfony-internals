<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Component\HttpFoundation\Response;

/**
 * First event dispatched. We are looking for a response now.
 */
class GetResponseEvent extends KernelEvent
{
    private $response;

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function hasResponse()
    {
        return $this->response !== null;
    }
}
