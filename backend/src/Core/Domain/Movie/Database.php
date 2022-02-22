<?php

declare(strict_types=1);

namespace Core\Domain\Movie;

interface Database
{
    public function search(string $wishTitle): ?MovieInfo;
}
