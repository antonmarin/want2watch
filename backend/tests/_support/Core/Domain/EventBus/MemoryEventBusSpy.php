<?php

declare(strict_types=1);

namespace tests\support\Core\Domain\EventBus;

use Core\Domain\DomainEvent;
use Core\UseCases\EventsBus;

final class MemoryEventBusSpy implements EventsBus
{
    public array $raisedEvents = [];

    public function raise(DomainEvent ...$event): void
    {
        array_push($this->raisedEvents, ...$event);
    }
}
