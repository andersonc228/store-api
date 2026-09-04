<?php

declare(strict_types=1);

namespace App\Shared\Application;

interface UuidGenerator
{
    public function create(): string;
}
