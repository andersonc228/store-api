<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface Event
{
    public function id(): string;
}
