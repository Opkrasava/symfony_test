<?php
namespace App\Exception;

use Exception;

class ApiException extends Exception
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation error', int $code = 0, \Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
