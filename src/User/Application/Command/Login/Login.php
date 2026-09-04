<?php

declare(strict_types=1);

namespace App\User\Application\Command\Login;

use App\Shared\Application\Bus\Command;
use App\Shared\Domain\Assert\Assert;
use SensitiveParameter;

readonly class Login implements Command
{
    public function __construct(
        public string $email,
        #[SensitiveParameter]
        public string $password,
    ) {
        Assert::lazy()
            ->that($email, 'email')->notEmpty()->email()
            ->that($password, 'password')->notEmpty()
            ->verifyNow();
    }
}
