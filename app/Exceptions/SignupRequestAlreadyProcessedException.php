<?php

namespace App\Exceptions;

use Exception;
use Throwable;


class SignupRequestAlreadyProcessedException extends Exception
{
    public function __construct(
        string $message = "This signup request has already been processed.",
        int $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
