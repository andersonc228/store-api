<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Messenger;

use App\Shared\Application\Bus\Query;
use App\Shared\Application\Bus\QueryBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class MessengerQueryBus implements QueryBus
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function ask(Query $query): mixed
    {
        /** @var HandledStamp $stamp */
        $stamp = $this->bus->dispatch($query)->last(HandledStamp::class);
        return $stamp->getResult();
    }
}
