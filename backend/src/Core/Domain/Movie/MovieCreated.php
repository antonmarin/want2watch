<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

use Core\Domain\DomainEvent;

final class MovieCreated implements DomainEvent
{
    private Movie $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    public function getMovie(): Movie
    {
        return $this->movie;
    }
}
