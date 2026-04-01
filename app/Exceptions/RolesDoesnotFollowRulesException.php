<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class RolesDoesnotFollowRulesException extends Exception
{
    public function __construct(
        string $message = "RolesDoesnotFollowRulesException",
        int $code = 0,
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
