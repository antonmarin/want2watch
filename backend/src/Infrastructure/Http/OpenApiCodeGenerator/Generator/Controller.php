<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator;

use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use cebe\openapi\spec\Operation;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    private Printer $printer;

    public function __construct()
    {
        $this->printer = new PsrPrinter();
    }

    public function generateFile(
        Operation $operation,
        string $operationPath,
        PhpNamespace $operationNamespace
    ): string {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace(clone $operationNamespace);
        $namespace->addUse(Route::class);
        $namespace->addUse(ResponseDTO::class);

        $class = $namespace->addClass('Controller');
        $class->setFinal();


        $callback = $class->addMethod('__invoke');
        $callback->addParameter('request')->setType($operationNamespace->getName() . '\Request');
        $callback->setReturnType(ResponseDTO::class);
        $callback->addComment(
            strtr(
                '@Route("{path}", name="{operationId}")',
                ['{path}' => $operationPath, '{operationId}' => $operation->operationId]
            )
        );
        $callback->addComment('@param Request $request');
        $callback->addComment('@return ResponseDTO');
        $responses = $operation->responses;
        if ($responses !== null) {
            $firstSuccessCode = $this->filterSuccessCode(array_keys($responses->getResponses()));
            if ($firstSuccessCode !== null) {
                $callback->addBody(strtr('return new Response{code}();', ['{code}' => $firstSuccessCode]));
            }
        }

        return $this->printer->printFile($file);
    }

    /**
     * @param int[] $statusCodes
     * @return ?int
     */
    private function filterSuccessCode(array $statusCodes): ?int
    {
        return array_values(
                array_filter(
                    $statusCodes,
                    static fn (int $statusCode) => (200 <= $statusCode) && ($statusCode < 300)
                )
            )[0] ?? null;
    }
}
