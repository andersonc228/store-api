<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\Command;
use App\Shared\Application\Bus\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class MessengerCommandBus implements CommandBus
{
    public function __construct(private MessageBusInterface $bus) {}

    public function dispatch(Command $command): mixed
    {
        /** @var ?HandledStamp $stamp */
        $stamp = $this->bus->dispatch($command)->last(HandledStamp::class);
        return $stamp?->getResult();
    }
}
