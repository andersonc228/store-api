<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Kernel\ExceptionLogTransformer;

use App\Shared\Infrastructure\Logging\ExceptionLogTransformer;
use Throwable;

readonly class DefaultExceptionLogTransformer implements ExceptionLogTransformer
{
    public function match(Throwable $exception): bool
    {
        return true;
    }

    public function message(Throwable $exception): string
    {
        return $exception->getMessage() ?: 'Empty exception message';
    }

    /** @return array<string, mixed> */
    public function context(Throwable $exception): array
    {
        return [
            'exception' => $exception,
        ];
    }

    public function level(Throwable $exception): string
    {
        return 'critical';
    }
}
