<?php

declare(strict_types=1);

namespace Core\UseCases;

use Core\Domain\Movie\Database;
use Core\Domain\Movie\LibrarianMovieRepository;
use Core\Domain\Movie\Movie;
use Psr\Log\LoggerInterface;

final class UpdateMovieInfo
{
    private Database $movieDatabase;
    private LibrarianMovieRepository $movieRepository;
    private EventsBus $eventsBus;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        EventsBus $eventsBus,
        Database $movieDatabase,
        LibrarianMovieRepository $movieRepository
    ) {
        $this->movieDatabase = $movieDatabase;
        $this->movieRepository = $movieRepository;
        $this->eventsBus = $eventsBus;
        $this->logger = $logger;
    }

    public function execute(string $wishTitle): void
    {
        $movieInfo = $this->movieDatabase->search($wishTitle);
        if ($movieInfo === null) {
            $this->logger->info("No $wishTitle movie info found in databases");
            return;
        }
        $movie = $this->movieRepository->findByTitle($wishTitle);
        if ($movie === null) {
            $createEvent = Movie::create($wishTitle);
            $movie = $createEvent->getMovie();
        }

        $events = array_merge(
            isset($createEvent) ? [$createEvent] : [],
            $movie->updateInfo($movieInfo),
        );

        $this->movieRepository->save($movie);
        $this->eventsBus->raise(...$events);
    }
}
