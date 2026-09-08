<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\EventBus;
use App\Shared\Common\Functional;
use App\Shared\Domain\Event;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

readonly class MessengerEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function publish(Event ...$events): void
    {
        Functional::each(
            fn (Event $event) => $this->bus->dispatch(
                new Envelope($event)->with(new DispatchAfterCurrentBusStamp())
            ),
            $events,
        );
    }
}
