<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

interface LibrarianMovieRepository
{
    public function findByTitle(string $wishTitle): ?Movie;

    public function save(Movie $movie): void;
}
