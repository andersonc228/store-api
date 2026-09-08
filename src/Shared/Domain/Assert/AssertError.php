<?php

declare(strict_types=1);

namespace App\Shared\Domain\Assert;

readonly class AssertError
{
    public function __construct(
        public string $property,
        public string $message
    ) {}
}
