<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel;

use JsonSerializable;

interface ResponseDTO extends JsonSerializable
{
    /**
     * @return int
     * @see \Symfony\Component\HttpFoundation\Response
     */
    public function getStatus(): int;

    /**
     * @return array<string, string> HeaderName => HeaderValue
     */
    public function getHeaders(): array;
}
