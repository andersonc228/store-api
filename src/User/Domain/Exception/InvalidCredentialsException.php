<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

use Exception;

class InvalidCredentialsException extends Exception
{
    public static function fromEmail(string $email): self
    {
        return new self(sprintf('Invalid credentials with email "%s"', $email));
    }
}
