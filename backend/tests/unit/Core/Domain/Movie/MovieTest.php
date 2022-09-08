<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

use PHPUnit\Framework\TestCase;

final class MovieTest extends TestCase
{
    /**
     * @test
     */
    public function shouldUpdatePosterAndNotTitleWhenUpdateByInfo(): void
    {
        $movie = new Movie('title', 'poster');
        $movieInfo = new MovieInfo('newTitle', 'newPoster');

        $events = $movie->updateInfo($movieInfo);

        self::assertCount(1, $events);

        $moviePosterUpdated = $events[0];
        self::assertInstanceOf(MoviePosterUpdated::class, $moviePosterUpdated);
        self::assertSame('title', $movie->getTitle());
        self::assertSame($movieInfo->getPoster(), $movie->getPoster());
    }
}
