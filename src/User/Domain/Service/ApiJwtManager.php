<?php

declare(strict_types=1);

namespace App\User\Domain\Service;

use App\User\Domain\Model\User;

interface ApiJwtManager
{
    public function create(User $user): string;
}
