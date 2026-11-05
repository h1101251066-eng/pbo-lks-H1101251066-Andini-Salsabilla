<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }
}