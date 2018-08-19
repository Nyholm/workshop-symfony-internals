<?php

declare(strict_types=1);

namespace App\Event;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Do changes to the response.
 */
class FilterResponseEvent extends KernelEvent
{
    private $response;

    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
