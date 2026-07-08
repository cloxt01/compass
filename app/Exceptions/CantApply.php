<?php

namespace App\Exceptions;

use Exception;

class CantApply extends Exception
{

    public function __construct($msg, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Tidak dapat melamar: " . $msg, $code, $previous);
    }

}
