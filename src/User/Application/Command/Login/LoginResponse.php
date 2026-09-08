<?php

declare(strict_types=1);

namespace App\User\Application\Command\Login;

use App\Shared\Application\Bus\Command;

readonly class LoginResponse implements Command
{
    public function __construct(
        public string $token,
        public int $expiresIn
    ) {}
}
