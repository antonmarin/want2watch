<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    /**
     * @Route("/hello", name="HelloWorld")
     * @param Request $request
     * @return FoundResponse
     */
    public function __invoke(Request $request): FoundResponse
    {
        return new FoundResponse('Hello ' . $request->getName());
    }
}
