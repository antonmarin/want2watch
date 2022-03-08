<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

final class MovieInfo
{
    private string $title;
    private string $poster;

    /**
     * @param string $title
     * @param string $poster
     */
    public function __construct(string $title, string $poster)
    {
        $this->title = $title;
        $this->poster = $poster;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPoster(): string
    {
        return $this->poster;
    }
}
