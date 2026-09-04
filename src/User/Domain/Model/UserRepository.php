<?php

declare(strict_types=1);

namespace App\User\Domain\Model;

use App\User\Domain\Exception\UserNotFoundException;
use App\User\Domain\Model\User;

interface UserRepository
{
    /** @throws UserNotFoundException */
    public function getById(string $id): User;
    public function findByEmail(string $email): ?User;
    public function save(User ...$users): void;
}
