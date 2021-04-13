<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use Symfony\Component\HttpFoundation\Response;

final class FoundResponse implements ResponseDTO
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message,
        ];
    }

    public function getStatus(): int
    {
        return Response::HTTP_OK;
    }

    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/json'];
    }
}
