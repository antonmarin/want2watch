<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\EventsBus;

use Core\Domain\DomainEvent;
use Core\UseCases\EventsBus;

final class DummyEventsBus implements EventsBus
{
    public function raise(DomainEvent ...$event): void
    {
    }
}
