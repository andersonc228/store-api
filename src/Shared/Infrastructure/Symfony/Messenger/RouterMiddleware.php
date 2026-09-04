<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\AsAsync;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

readonly class RouterMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $consumerBusName,
        private string $highTransportName,
        private string $mediumTransportName,
        private string $lowTransportName,
    ) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $async = $this->resolveAsAsync($envelope->getMessage()::class);

        return $stack
            ->next()
            ->handle(
                $envelope
                    ->with(
                        new TransportNamesStamp([$this->resolveTransportName($async)]),
                        new BusNameStamp($this->consumerBusName),
                        new AsyncRetryStamp(
                            $async->maxRetries,
                            $async->delayMs,
                            $async->multiplier,
                            $async->jitter,
                            $async->recordFailure,
                        ),
                    ),
                $stack,
            );
    }

    /** @param class-string $messageClass */
    private function resolveAsAsync(string $messageClass): AsAsync
    {
        /** @var ReflectionAttribute<AsAsync>[] $attributes */
        $attributes = new ReflectionClass($messageClass)->getAttributes(AsAsync::class);

        return $attributes !== [] ? $attributes[0]->newInstance() : new AsAsync();
    }

    private function resolveTransportName(AsAsync $async): string
    {
        return match ($async->priority) {
            AsAsync::PRIORITY_HIGH => $this->highTransportName,
            AsAsync::PRIORITY_LOW => $this->lowTransportName,
            default => $this->mediumTransportName,
        };
    }
}
