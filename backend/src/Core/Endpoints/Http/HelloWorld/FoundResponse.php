<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

use Symfony\Component\HttpFoundation\JsonResponse;

final class FoundResponse
{

    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
