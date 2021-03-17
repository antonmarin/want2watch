<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    /**
     * @Route("/hello", name="hello_world")
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: Implement
        $response = new FoundResponse('Hello ' . $request->getName());
        return new JsonResponse($response);
    }
}
