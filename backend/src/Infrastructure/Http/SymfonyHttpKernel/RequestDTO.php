<?php

declare(strict_types=1);

namespace Infrastructure\Http\SymfonyHttpKernel;

use Symfony\Component\HttpFoundation\Request;

interface RequestDTO
{
    public function __construct(Request $request);
}
