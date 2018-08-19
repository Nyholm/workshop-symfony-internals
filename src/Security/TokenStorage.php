<?php

declare(strict_types=1);

namespace App\Security;

class TokenStorage
{
    private $tokens = [];

    /**
     * @return mixed
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @return mixed
     */
    public function getLastToken()
    {
        $count = count($this->tokens);

        if (isset($this->tokens[$count - 1])) {
            return $this->tokens[$count - 1];
        }

        return null;
    }

    /**
     * @param mixed $tokens
     */
    public function addToken($tokens)
    {
        $this->tokens[] = $tokens;
    }
}
