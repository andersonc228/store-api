<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

use Exception;

class MessageNotRegistered extends Exception
{
    public static function from(string $class): self
    {
        return new self(sprintf('Handler for message %s not registered', $class));
    }
}
