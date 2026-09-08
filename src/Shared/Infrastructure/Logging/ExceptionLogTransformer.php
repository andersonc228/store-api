<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Logging;

use Throwable;

interface ExceptionLogTransformer
{
    public function match(Throwable $exception): bool;

    public function message(Throwable $exception): string;

    /** @return array<string, mixed> */
    public function context(Throwable $exception): array;

    public function level(Throwable $exception): string;
}
