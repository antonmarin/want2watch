<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\GoogleSpreadsheets;

use Core\Domain\Movie\Movie;
use Google\Service\Sheets;

final class Fixtures
{
    private const SHEET_RANGE = 'Перечень фильмов!A2:B';
    private const TESTING_SHEET_ID = "1-2dClUwM1OHx1bYXtDMrDm5NjULGmJktYUOdI75fZ1I";
    private Sheets $sheets;

    public function __construct(Sheets $sheets)
    {
        $this->sheets = $sheets;
    }

    /**
     * @return array[]
     */
    public function getData(): array
    {
        $values = $this->sheets->spreadsheets_values->get(
            self::TESTING_SHEET_ID,
            self::SHEET_RANGE
        );
        return $values->getValues();
    }

    public function clear(): void
    {
        $this->sheets->spreadsheets_values->clear(
            self::TESTING_SHEET_ID,
            self::SHEET_RANGE,
            new Sheets\ClearValuesRequest(),
        );
    }

    public function movie(string $title, string $poster): Movie
    {
        $valueRange = new Sheets\ValueRange();
        $valueRange->setRange(self::SHEET_RANGE);
        $valueRange->setValues(
            [
                [$title, $poster],
            ]
        );
        $this->sheets->spreadsheets_values->update(
            self::TESTING_SHEET_ID,
            self::SHEET_RANGE,
            $valueRange,
            ['valueInputOption' => 'USER_ENTERED']
        );

        return new Movie($title, $poster);
    }
}
