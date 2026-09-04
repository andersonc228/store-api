<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Application\UuidGenerator;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

class SymfonyUuidGenerator implements UuidGenerator
{
    public function create(): string
    {
        return SymfonyUuid::v4()->toString();
    }
}
