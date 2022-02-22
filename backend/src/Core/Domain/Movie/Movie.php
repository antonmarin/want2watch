<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

use Core\Domain\DomainEvent;

final class Movie
{
    public static function create(): MovieCreated
    {
        return new MovieCreated(new self());
    }

    /**
     * @param MovieInfo $movieInfo
     * @return DomainEvent[]
     */
    public function updateInfo(MovieInfo $movieInfo): array
    {
        return [
            new MovieImageUpdated(),
        ];
    }
}
