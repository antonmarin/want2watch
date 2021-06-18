<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator;

final class Specification
{
    private string $specificationPath;

    public function __construct(string $specificationPath)
    {
        $this->specificationPath = $specificationPath;
    }

    public function generateCode(): void
    {
        echo $this->specificationPath;
    }
}
