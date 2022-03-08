<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\MovieDatabase;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class IMDBTest extends KernelTestCase
{
    private string $cacheDir;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->cacheDir = $kernel->getCacheDir();
        $this->logger = new NullLogger();
    }

    /**
     * @test
     */
    public function shouldFindFirstFoundMovieInfoWhenMultipleFound(): void
    {
        $database = new IMDB($this->logger, $this->cacheDir);

        $movieInfo = $database->search('Изобретение лжи');

        self::assertEquals('The Invention of Lying', $movieInfo->getTitle());
        self::assertEquals(
            'https://m.media-amazon.com/images/M/MV5BMTU2OTQzOTc1Nl5BMl5BanBnXkFtZTcwNDM5MDE4Mg@@.jpg',
            $movieInfo->getPoster()
        );
    }
}
