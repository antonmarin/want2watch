<?php

declare(strict_types=1);

namespace Infrastructure\Integrations\MovieDatabase;

use Core\Domain\Movie\Database;
use Core\Domain\Movie\MovieInfo;

final class NullDatabase implements Database
{
    public function search(string $wishTitle): ?MovieInfo
    {
        return null;
    }
}
