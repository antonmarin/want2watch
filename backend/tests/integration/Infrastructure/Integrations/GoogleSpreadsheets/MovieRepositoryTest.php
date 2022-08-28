<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\GoogleSpreadsheets;

use Core\Domain\Movie\MovieInfo;
use Google\Service\Sheets;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MovieRepositoryTest extends KernelTestCase
{
    private MovieRepository $repository;
    private Fixtures $fixtures;

    /**
     * @test
     */
    public function shouldFindExistedMovie(): void
    {
        $wishTitle = 'Изобретение лжи';
        $wishPoster = "https://avatars.mds.yandex.net/get-ott/224348/2a0000016e1e54e5fee45a86eedce5e11798/orig";
        $this->fixtures->movie($wishTitle, $wishPoster);

        $found = $this->repository->findByTitle($wishTitle);

        self::assertNotNull($found);
        self::assertSame($wishTitle, $found->getTitle());
        self::assertSame($wishPoster, $found->getPoster());
    }

    public function shouldAddNewMovie(): void
    {
    }

    /**
     * @test
     */
    public function shouldUpdateExistedMovie(): void
    {
        $existedWishTitle = 'Изобретение лжи';
        $existedWishPoster = "https://avatars.mds.yandex.net/get-ott/224348/2a0000016e1e54e5fee45a86eedce5e11798/orig";
        $movie = $this->fixtures->movie($existedWishTitle, $existedWishPoster);

        $movieInfo = new MovieInfo('Updated title', 'http://some.new.poster');
        $movie->updateInfo($movieInfo);
        $this->repository->save($movie);

        $data = $this->fixtures->getData();
        self::assertCount(1, $data);
        self::assertSame([$existedWishTitle, $movieInfo->getPoster()], $data[0]);
    }

    protected function setUp(): void
    {
        $sheets = self::getContainer()->get(Sheets::class);
        $this->repository = new MovieRepository($sheets);
        $this->fixtures = new Fixtures($sheets);
        $this->fixtures->clear();
    }
}
