<?php

declare(strict_types=1);

namespace Core\UseCases;

use Core\Domain\DomainEvent;

interface EventsBus
{
    public function raise(DomainEvent ...$event): void;
}
