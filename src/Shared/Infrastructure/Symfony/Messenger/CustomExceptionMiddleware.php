<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\MessageNotRegistered;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

readonly class CustomExceptionMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (NoHandlerForMessageException $ex) {
            throw MessageNotRegistered::from(get_class($envelope->getMessage()));
        } catch (HandlerFailedException $ex) {
            /** @var Throwable $previous */
            $previous = $ex->getPrevious();
            throw $previous;
        } catch (Throwable $t) {
            throw $t;
        }
    }
}
