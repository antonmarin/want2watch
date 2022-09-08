<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\Null;

use Core\Domain\Movie\Database;
use Core\Domain\Movie\MovieInfo;

final class MovieDatabase implements Database
{
    public function search(string $wishTitle): ?MovieInfo
    {
        return null;
    }
}
