<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

final class Movie
{
    private string $title;
    private ?string $poster;

    public function __construct(string $title, string $poster = null)
    {
        $this->title = $title;
        $this->poster = $poster;
    }

    public static function create(string $title): MovieCreated
    {
        return new MovieCreated(new self($title));
    }

    /**
     * @param MovieInfo $movieInfo
     * @return MoviePosterUpdated[]
     */
    public function updateInfo(MovieInfo $movieInfo): array
    {
        $this->poster = $movieInfo->getPoster();
        return [
            new MoviePosterUpdated(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }
}
