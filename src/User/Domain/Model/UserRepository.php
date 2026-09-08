<?php

declare(strict_types=1);

namespace App\User\Domain\Model;

use App\User\Domain\Exception\UserNotFoundException;

interface UserRepository
{
    /** @throws UserNotFoundException */
    public function findByEmail(string $email): ?User;

    public function save(User ...$users): void;

    public function count(): int;
}
