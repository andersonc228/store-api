<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

interface QueryBus
{
    /** @throws MessageNotRegistered */
    public function ask(Query $query): mixed;
}
