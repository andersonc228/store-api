<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Kernel\ExceptionLogTransformer;

use App\Shared\Infrastructure\Logging\ExceptionLogTransformer;
use Throwable;

readonly class ConfigExceptionLogTransformer implements ExceptionLogTransformer
{
    /** @param array<string, string> $mapping */
    public function __construct(private array $mapping) {}

    public function match(Throwable $exception): bool
    {
        return array_key_exists(get_class($exception), $this->mapping);
    }

    public function message(Throwable $exception): string
    {
        return $exception->getMessage() ?: 'Empty exception message';
    }

    /** @return array<string, mixed> */
    public function context(Throwable $exception): array
    {
        return ['exception' => $exception];
    }

    public function level(Throwable $exception): string
    {
        return $this->mapping[get_class($exception)] ?: 'critical';
    }
}
