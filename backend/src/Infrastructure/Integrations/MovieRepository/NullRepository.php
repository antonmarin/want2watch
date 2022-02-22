<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\MovieRepository;

use Core\Domain\Movie\LibrarianMovieRepository;
use Core\Domain\Movie\Movie;

final class NullRepository implements LibrarianMovieRepository
{
    public function findByTitle(string $wishTitle): ?Movie
    {
        return null;
    }

    public function save(Movie $movie): void
    {
    }
}
