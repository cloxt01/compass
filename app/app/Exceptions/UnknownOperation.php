<?php

namespace App\Exceptions;

class UnknownOperation extends Exception
{
    public function __construct($message = "Operation tidak ditemukan", $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
}