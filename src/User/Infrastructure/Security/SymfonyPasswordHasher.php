<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\Shared\Domain\Service\PasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

readonly class SymfonyPasswordHasher implements PasswordHasher
{
    private PasswordHasherInterface $passwordHasher;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->passwordHasher = $hasherFactory->getPasswordHasher('common');
    }

    public function hash(string $password): string
    {
        return $this->passwordHasher->hash($password);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        return $this->passwordHasher->verify($hashedPassword, $plainPassword);
    }
}
