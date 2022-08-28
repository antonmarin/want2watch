<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\MovieDatabase;

use Core\Domain\Movie\Database;
use Core\Domain\Movie\MovieInfo;
use Psr\Log\LoggerInterface;

final class IMDB implements Database
{
    private LoggerInterface $logger;
    private \hmerritt\Imdb $db;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->db = new \hmerritt\Imdb();
    }

    public function search(string $wishTitle): ?MovieInfo
    {
        $result = $this->db->film($wishTitle);
        $this->logger->debug('Found title: {$result}', ['result' => $result]);
        if ($result['id'] === "") {
            return null;
        }

        return new MovieInfo(
            $result['title'],
            $result['poster'],
        );
    }
}
