<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\Null;

use Core\Domain\Movie\LibrarianMovieRepository;
use Core\Domain\Movie\Movie;

final class MovieRepository implements LibrarianMovieRepository
{
    public function findByTitle(string $wishTitle): ?Movie
    {
        return null;
    }

    public function save(Movie $movie): void
    {
    }
}
