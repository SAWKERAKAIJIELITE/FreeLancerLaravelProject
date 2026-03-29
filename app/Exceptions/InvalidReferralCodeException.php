<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidReferralCodeException extends Exception
{
    public function __construct(
        string $message = "InvalidReferralCodeException",
        int $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
