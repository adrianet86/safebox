<?php

namespace AdsMurai\Application\Service\SafeBox;

use Exception;
use Throwable;

class CommonPasswordException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}