<?php

namespace App\Exceptions;

class UnknownProvider extends Exception
{
    public function __construct($provider , $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct(`Provider not supported: ${$provider}`, $code, $previous);
    }
    
}