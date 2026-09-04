<?php

declare(strict_types=1);

namespace App\User\Domain\Exception;

use Exception;

class UserNotFoundException extends Exception
{
    public static function fromEmail(string $email): self
    {
        return new self(sprintf('User with email "%s" not found', $email));
    }
}
