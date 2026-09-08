<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Kernel;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Psr\Log\LoggerInterface;
use App\Api\Ui\Kernel\ExceptionTransformer\ExceptionTransformer;
use App\Shared\Infrastructure\Logging\ExceptionLogTransformer;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

#[AsEventListener]
readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private RequestStack $requestStack;
    /** @var iterable<ExceptionTransformer> */
    private iterable $exceptionTransformers;
    /** @var iterable<ExceptionLogTransformer> */
    private iterable $exceptionLogTransformers;

    /**
     * @param iterable<ExceptionTransformer> $exceptionTransformers
     * @param iterable<ExceptionLogTransformer> $exceptionLogTransformers
     */
    public function __construct(
        LoggerInterface $logger,
        RequestStack $requestStack,
        iterable $exceptionTransformers,
        iterable $exceptionLogTransformers,
    ) {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->exceptionTransformers = $exceptionTransformers;
        $this->exceptionLogTransformers = $exceptionLogTransformers;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['logSfEvent', 1000],
                ['responseSfEvent', 999],
            ],
            Events::AUTHENTICATION_FAILURE => [
                ['logJwtEvent', 1000],
                ['responseJwtEvent', 999],
            ],
            Events::JWT_INVALID => [
                ['logJwtEvent', 1000],
                ['responseJwtEvent', 999],
            ],
            Events::JWT_EXPIRED => [
                ['logJwtEvent', 1000],
                ['responseJwtEvent', 999],
            ],
        ];
    }

    public function logSfEvent(ExceptionEvent $event): void
    {
        $this->toLog($event->getThrowable());
    }

    public function responseSfEvent(ExceptionEvent $event): void
    {
        if ($response = $this->toResponse($event->getThrowable())) {
            $event->setResponse($response);
        }
    }

    public function logJwtEvent(AuthenticationFailureEvent $event): void
    {
        $this->toLog($event->getException());
    }

    public function responseJwtEvent(AuthenticationFailureEvent $event): void
    {
        if ($response = $this->toResponse($event->getException())) {
            $event->setResponse($response);
        }
    }

    private function toLog(Throwable $exception): void
    {
        foreach ($this->exceptionLogTransformers as $exceptionLogTransformers) {
            if ($exceptionLogTransformers->match($exception)) {
                $this->logger->log(
                    $exceptionLogTransformers->level($exception),
                    $exceptionLogTransformers->message($exception),
                    $exceptionLogTransformers->context($exception),
                );
                break;
            }
        }
    }

    private function toResponse(Throwable $exception): ?Response
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request && !str_starts_with($request->getPathInfo(), '/api/')) {
            return null;
        }
        foreach ($this->exceptionTransformers as $exceptionTransformer) {
            if ($exceptionTransformer->match($exception)) {
                return $exceptionTransformer->transform($exception);
            }
        }

        return null;
    }
}
