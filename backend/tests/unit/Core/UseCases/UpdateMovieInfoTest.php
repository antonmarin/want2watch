<?php

declare(strict_types=1);

namespace Core\UseCases;

use Core\Domain\Movie\Database;
use Core\Domain\Movie\LibrarianMovieRepository;
use Core\Domain\Movie\Movie;
use Core\Domain\Movie\MovieCreated;
use Core\Domain\Movie\MovieInfo;
use Core\Domain\Movie\MoviePosterUpdated;
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
        $movieTitle = "someTitle";
        $this->movieDatabase->method('search')->willReturn(new MovieInfo($movieTitle, "http://poster.ru"));
        $this->movieRepository->method('findByTitle')->willReturn(new Movie($movieTitle));

        $this->case->execute('some');

        $expectedEvents = [MoviePosterUpdated::class];
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
        $this->movieDatabase->method('search')->willReturn(new MovieInfo("someTitle", "http://poster.ru"));
        $this->movieRepository->method('findByTitle')->willReturn(null);

        $this->case->execute('some');

        $expectedEvents = [MovieCreated::class, MoviePosterUpdated::class];
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
