<?php

declare(strict_types=1);

namespace App\Event;

use Psr\Http\Message\ResponseInterface;

/**
 * First event dispatched. We are looking for a response now.
 */
class GetResponseEvent extends KernelEvent
{
    private $response;

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function hasResponse()
    {
        return $this->response !== null;
    }
}
