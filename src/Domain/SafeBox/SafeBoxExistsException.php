<?php

namespace AdsMurai\Domain\SafeBox;

use Exception;
use Throwable;

class SafeBoxExistsException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}