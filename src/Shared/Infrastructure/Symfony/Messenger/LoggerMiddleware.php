<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\Command;
use App\Shared\Application\Bus\Query;
use App\Shared\Domain\Event;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use SensitiveParameter;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Throwable;

readonly class LoggerMiddleware implements MiddlewareInterface
{
    /** @param string[] $ignoreClasses */
    public function __construct(
        private LoggerInterface $logger,
        private array $ignoreClasses = [],
    ) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        if ($this->mustBeIgnored($message)) {
            return $stack->next()->handle($envelope, $stack);
        }
        try {
            $envelope = $stack->next()->handle($envelope, $stack);
            $isSent = (bool) $envelope->last(SentStamp::class);
            $this->logger->info($this->readableName($message, !$isSent), $this->serialize($message));
            return $envelope;
        } catch (Throwable $t) {
            $isReceived = (bool) $envelope->last(ReceivedStamp::class);
            $context = $this->serialize($message);
            if ($isReceived) {
                $context['retry_count'] = RedeliveryStamp::getRetryCountFromEnvelope($envelope);
            }
            $label = $isReceived
                ? sprintf('Command %s failed', $message::class)
                : $this->readableName($message, true);
            $this->logger->warning($label, $context);
            throw $t;
        }
    }

    private function mustBeIgnored(object $object): bool
    {
        return in_array(get_class($object), $this->ignoreClasses, true);
    }

    private function readableName(object $message, bool $isConsumed): string
    {
        return match (true) {
            $message instanceof Command && $isConsumed => sprintf(
                'Command %s was dispatched',
                get_class($message),
            ),
            $message instanceof Command && !$isConsumed => sprintf( // @phpstan-ignore-line
                'Command %s was enqueued',
                get_class($message),
            ),
            $message instanceof Query && $isConsumed => sprintf(
                'Query %s was asked',
                get_class($message),
            ),
            $message instanceof Query && !$isConsumed => sprintf( // @phpstan-ignore-line
                'Query %s was enqueued',
                get_class($message),
            ),
            $message instanceof Event && $isConsumed => sprintf(
                'Event %s related to id %s was consumed',
                get_class($message),
                $message->id(),
            ),
            $message instanceof Event && !$isConsumed => sprintf( // @phpstan-ignore-line
                'Event %s related to id %s was published',
                get_class($message),
                $message->id(),
            ),
            $isConsumed => sprintf(
                'Message %s was dispatched',
                get_class($message),
            ),
            default => sprintf(
                'Message %s was enqueued',
                get_class($message),
            ),
        };
    }

    /** @return mixed[] */
    private function serialize(object $object): array
    {
        return array_merge($this->redactSensitive($object), [
            'class' => get_class($object),
        ]);
    }

    /** @return mixed[] */
    private function redactSensitive(object $object): array
    {
        $payload = (array) $object;
        $constructor = new ReflectionClass($object)->getConstructor();
        if (!$constructor) {
            return $payload;
        }
        foreach ($constructor->getParameters() as $param) {
            if (!$param->getAttributes(SensitiveParameter::class)) {
                continue;
            }
            $name = $param->getName();
            if (!array_key_exists($name, $payload)) {
                continue;
            }
            $payload[$name] = $this->hash($payload[$name]);
        }
        return $payload;
    }

    private function hash(mixed $value): string
    {
        $serialized = is_scalar($value) || $value === null
            ? (string) $value
            : serialize($value);
        return 'sha256:' . substr(hash('sha256', $serialized), 0, 8);
    }
}
