<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestDTOResolver implements ArgumentValueResolverInterface
{

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        // TODO: Implement supports() method.
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        // TODO: Implement resolve() method.
    }
}
