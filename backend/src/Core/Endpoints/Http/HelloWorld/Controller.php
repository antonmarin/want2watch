<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

final class Controller
{
    public function __invoke(Request $request): FoundResponse
    {
        // TODO: Implement
        return new FoundResponse();
    }
}
