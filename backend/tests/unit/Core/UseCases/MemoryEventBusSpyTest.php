<?php

declare(strict_types=1);

namespace Core\UseCases;

use Core\Domain\DomainEvent;
use PHPUnit\Framework\TestCase;
use tests\support\Core\Domain\EventBus\MemoryEventBusSpy;

final class MemoryEventBusSpyTest extends TestCase
{
    /**
     * @test
     */
    public function shouldStoreRaisedEvents(): void
    {
        $spy = new MemoryEventBusSpy();
        $sutEvent = $this->createMock(DomainEvent::class);
        $spy->raise($sutEvent);

        self::assertSame([$sutEvent], $spy->raisedEvents);
    }
}
