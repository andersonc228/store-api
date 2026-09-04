<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

readonly class AsyncRetryStamp implements StampInterface
{
    public function __construct(
        public int $maxRetries,
        public int $delayMs,
        public float $multiplier,
        public float $jitter,
        public bool $recordFailure = true,
    ) {}
}
