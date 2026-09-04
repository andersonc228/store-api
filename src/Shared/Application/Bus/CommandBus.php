<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

interface CommandBus
{
    /** @throws MessageNotRegistered */
    public function dispatch(Command $command): mixed;
}
