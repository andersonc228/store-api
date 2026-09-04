<?php

declare(strict_types=1);

namespace App\Shared\Domain;

trait EventsTrait
{
    /** @var Event[] */
    private array $events = [];

    /** @return Event[] */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    protected function recordEvent(Event ...$events): void
    {
        $this->events = [...$this->events, ...$events];
    }
}
