<?php

namespace App\Exceptions;

class UnknownOperation extends Exception
{
    public function __construct($operation , $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct(`Operation not supported: ${$operation}`, $code, $previous);
    }
    
}