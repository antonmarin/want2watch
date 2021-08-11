<?php

declare(strict_types=1);

namespace EventsCollector\Endpoints\Http\Glide\UserAddedWantie;

use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;

final class Response204 implements ResponseDTO
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [];
    }

    public function getStatus(): int
    {
        return 204;
    }

    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/json'];
    }
}
