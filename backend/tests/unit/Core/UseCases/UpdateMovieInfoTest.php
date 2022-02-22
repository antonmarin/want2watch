<?php

declare(strict_types=1);

namespace Core\UseCases;

use Core\Domain\Movie\Database;
use Core\Domain\Movie\LibrarianMovieRepository;
use Core\Domain\Movie\Movie;
use Core\Domain\Movie\MovieCreated;
use Core\Domain\Movie\MovieImageUpdated;
use Core\Domain\Movie\MovieInfo;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use tests\support\Core\Domain\EventBus\MemoryEventBusSpy;

final class UpdateMovieInfoTest extends TestCase
{
    private MemoryEventBusSpy $eventBus;
    private LoggerInterface $logger;
    private Database $movieDatabase;
    private LibrarianMovieRepository $movieRepository;
    private UpdateMovieInfo $case;

    /**
     * @test
     */
    public function shouldUpdateMovieImageWhenMovieInfoFoundAndSuccessfullyStored(): void
    {
        $this->movieDatabase->method('search')->willReturn(new MovieInfo());
        $this->movieRepository->method('findByTitle')->willReturn(new Movie());

        $this->case->execute('some');

        $expectedEvents = [MovieImageUpdated::class];
        foreach ($expectedEvents as $key => $class) {
            self::assertInstanceOf($class, $this->eventBus->raisedEvents[$key]);
        }
    }

    /**
     * @test
     */
    public function shouldLogAndVoidWhenMovieInfoNotFound(): void
    {
        $this->movieDatabase->method('search')->willReturn(null);

        $this->case->execute('some');

        $this->logger->hasRecords('info');
        self::assertSame([], $this->eventBus->raisedEvents);
    }

    /**
     * @test
     */
    public function shouldCreateNewMovieWhenMovieNotFoundInStorage(): void
    {
        $this->movieDatabase->method('search')->willReturn(new MovieInfo());
        $this->movieRepository->method('findByTitle')->willReturn(null);

        $this->case->execute('some');

        $expectedEvents = [MovieCreated::class, MovieImageUpdated::class];
        foreach ($expectedEvents as $key => $class) {
            self::assertInstanceOf($class, $this->eventBus->raisedEvents[$key]);
        }
    }

    protected function setUp(): void
    {
        $this->eventBus = new MemoryEventBusSpy();
        $this->movieDatabase = $this->createMock(Database::class);
        $this->movieRepository = $this->createMock(LibrarianMovieRepository::class);
        $this->logger = new TestLogger();
        $this->case = new UpdateMovieInfo(
            $this->logger, $this->eventBus, $this->movieDatabase, $this->movieRepository
        );
    }
}
