<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core\Endpoints\Http\HelloWorld;

use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    /**
     * @Route("/hello", name="HelloWorld")
     * @param Request $request
     * @return Response200
     */
    public function __invoke(Request $request): Response200
    {
        if ($request->getName() !== 'asdf') {
            trigger_error('unknown error', E_ERROR);
        }
        return new Response200('Hello ' . $request->getName());
    }
}
