<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

use App\Shared\Domain\Event;

interface EventBus
{
    public function publish(Event ...$events): void;
}
