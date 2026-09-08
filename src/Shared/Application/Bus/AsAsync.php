<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class AsAsync
{
    public const string PRIORITY_HIGH = 'high';
    public const string PRIORITY_MEDIUM = 'medium';
    public const string PRIORITY_LOW = 'low';
    public const int DEFAULT_MAX_RETRIES = 2;
    public const int DEFAULT_DELAY_MS = 300_000;
    public const float DEFAULT_MULTIPLIER = 3.0;
    public const float DEFAULT_JITTER = 0.1;

    public function __construct(
        public string $priority = self::PRIORITY_MEDIUM,
        public int $maxRetries = self::DEFAULT_MAX_RETRIES,
        public int $delayMs = self::DEFAULT_DELAY_MS,
        public float $multiplier = self::DEFAULT_MULTIPLIER,
        public float $jitter = self::DEFAULT_JITTER,
        public bool $recordFailure = true,
    ) {}
}
