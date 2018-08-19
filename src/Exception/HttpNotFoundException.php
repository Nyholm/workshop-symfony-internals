<?php

declare(strict_types=1);

namespace App\Exception;

use Psr\Http\Message\RequestInterface;

class HttpNotFoundException extends \RuntimeException
{
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        parent::__construct('Could not find a response for request');
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
