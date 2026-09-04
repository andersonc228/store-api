<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\AsAsync;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Throwable;

readonly class StampRetryStrategy implements RetryStrategyInterface
{
    public function __construct(
        private int $defaultMaxRetries = AsAsync::DEFAULT_MAX_RETRIES,
        private int $defaultDelayMs = AsAsync::DEFAULT_DELAY_MS,
        private float $defaultMultiplier = AsAsync::DEFAULT_MULTIPLIER,
        private float $defaultJitter = AsAsync::DEFAULT_JITTER,
    ) {}

    public function isRetryable(Envelope $message, ?Throwable $throwable = null): bool
    {
        $stamp = $this->getStamp($message);
        $retryCount = RedeliveryStamp::getRetryCountFromEnvelope($message);

        return $retryCount < $stamp->maxRetries;
    }

    public function getWaitingTime(Envelope $message, ?Throwable $throwable = null): int
    {
        $stamp = $this->getStamp($message);
        $retryCount = RedeliveryStamp::getRetryCountFromEnvelope($message);

        $delay = $stamp->delayMs * ($stamp->multiplier ** $retryCount);

        if ($stamp->jitter > 0) {
            $delay *= 1 + $stamp->jitter * (2 * mt_rand() / mt_getrandmax() - 1);
        }

        return (int) ceil($delay);
    }

    private function getStamp(Envelope $message): AsyncRetryStamp
    {
        /** @var ?AsyncRetryStamp $stamp */
        $stamp = $message->last(AsyncRetryStamp::class);

        return $stamp ?? new AsyncRetryStamp(
            $this->defaultMaxRetries,
            $this->defaultDelayMs,
            $this->defaultMultiplier,
            $this->defaultJitter,
            true,
        );
    }
}
