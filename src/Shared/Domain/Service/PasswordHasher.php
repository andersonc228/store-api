<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

interface PasswordHasher
{
    public function hash(string $password): string;

    public function verify(string $hashedPassword, string $plainPassword): bool;
}
