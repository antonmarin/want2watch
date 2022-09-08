<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\GoogleSpreadsheets;

use Core\Domain\Movie\LibrarianMovieRepository;
use Core\Domain\Movie\Movie;
use Google\Service\Sheets;

final class MovieRepository implements LibrarianMovieRepository
{
    private const SHEET_LIST = 'Перечень фильмов';
    private const DATA_RANGE = 'Перечень фильмов!A2:B';
    private string $spreadsheetId = "1-2dClUwM1OHx1bYXtDMrDm5NjULGmJktYUOdI75fZ1I";
    private Sheets $service;

    public function __construct(Sheets $service)
    {
        $this->service = $service;
    }

    public function findByTitle(string $wishTitle): ?Movie
    {
        $movies = $this->populateRows($this->getAll());

        return $movies[$wishTitle] ?? null;
    }

    /**
     * @param array[] $rows each [0: title as string, 1: poster as uri]
     * @return Movie[]
     */
    private function populateRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[$row[0]] = new Movie($row[0], $row[1] ?? null);
        }

        return $result;
    }

    public function save(Movie $movie): void
    {
        $range = $this->findRangeForMovie($movie);
        if ($range === null) {
            return;
        }

        $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range->getRange(),
            $range,
            [
                'valueInputOption' => 'USER_ENTERED',
            ]
        );
    }

    /**
     * @return array[] keys = rows number
     */
    private function getAll(): array
    {
        $data = $this->service->spreadsheets_values
            ->get($this->spreadsheetId, self::DATA_RANGE)
            ->getValues();
        array_unshift($data, []);
        array_unshift($data, []);
        unset($data[0], $data[1]);

        return $data;
    }

    /**
     * @param Movie $movie
     * @return Sheets\ValueRange<int,array>|null where array is row of movie values: title, poster
     */
    private function findRangeForMovie(Movie $movie): ?Sheets\ValueRange
    {
        $data = $this->getAll();
        foreach ($data as $rowNumber => $row) {
            if ($row[0] === $movie->getTitle()) {
                $body = new Sheets\ValueRange();
                $body->setRange($this->prepareRangeForRowNumber($rowNumber));
                $body->setValues([[$row[0], $movie->getPoster()]]);

                return $body;
            }
        }

        return null;
    }

    private function prepareRangeForRowNumber(int $rowN): string
    {
        return self::SHEET_LIST . "!A$rowN:B$rowN";
    }
}
