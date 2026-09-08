<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Infrastructure\Logging\ExceptionLogTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Throwable;

readonly class WorkerFailedLogSubscriber implements EventSubscriberInterface
{
    /** @param ExceptionLogTransformer[] $exceptionLogTransformers */
    public function __construct(
        private LoggerInterface $logger,
        private array $exceptionLogTransformers,
    ) {}

    /** @return array<string, array{string, int}> */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => ['onWorkerMessageFailed', 50],
        ];
    }

    public function onWorkerMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $exception = $this->unwrap($event->getThrowable());

        $this->logger->log(
            $this->resolveLevel($exception),
            $exception->getMessage() ?: 'Empty exception message',
            [
                'exception' => $exception,
            ],
        );
    }

    private function resolveLevel(Throwable $exception): string
    {
        foreach ($this->exceptionLogTransformers as $transformer) {
            if ($transformer->match($exception)) {
                return $transformer->level($exception);
            }
        }

        return 'critical';
    }

    private function unwrap(Throwable $exception): Throwable
    {
        if ($exception instanceof HandlerFailedException && $exception->getPrevious()) {
            return $exception->getPrevious();
        }

        return $exception;
    }
}
