<?php

declare(strict_types=1);

namespace Core\Endpoints\Http\HelloWorld;

use Symfony\Component\Routing\Annotation\Route;
use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;

final class Controller
{
    /**
     * @Route("/hello", name="HelloWorld")
     * @param Request $request
     * @return ResponseDTO
     */
    public function __invoke(Request $request): ResponseDTO
    {
        if ($request->getName() !== 'asdf') {
            trigger_error('unknown error', E_ERROR);
        }
        return new Response200('Hello ' . $request->getName());
    }
}
