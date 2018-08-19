<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Request;

class HttpNotFoundException extends \RuntimeException
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct('Could not find a response for request');
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
