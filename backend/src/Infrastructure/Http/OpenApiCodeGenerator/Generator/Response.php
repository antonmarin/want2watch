<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator;

use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\PsrPrinter;

final class Response
{
    private Printer $printer;

    public function __construct()
    {
        $this->printer = new PsrPrinter();
    }

    public function generateFile(
        \cebe\openapi\spec\Response $response,
        int $statusCode,
        PhpNamespace $operationNamespace
    ): string {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace(clone $operationNamespace);
        $namespace->addUse(ResponseDTO::class);

        $class = $namespace->addClass('Response' . $statusCode);
        $class->setFinal()->addImplement(ResponseDTO::class);
        $constructor = $class->addMethod('__construct');
        $serializer = $class->addMethod('jsonSerialize')->setReturnType('array');
        $serializer->addComment('{@inheritdoc}')->addComment('@return array<string,string>');
        $serializer->addBody('return [');
        $contentType = 'application/json';
        foreach ($response->content[$contentType]->schema->properties as $propertyName => $property) {
            $class->addProperty($propertyName)->setPrivate()->setType($property->type);
            $constructor->addParameter($propertyName)->setType($property->type);
            $constructor->addBody('$this->? = $?;', [$propertyName,$propertyName]);
            $serializer->addBody('    ? => $this->?,', [$propertyName,$propertyName]);
        }
        $serializer->addBody('];');
        $class->addMethod('getStatus')
            ->setReturnType('int')
            ->addBody('return ?;', [$statusCode]);
        $class->addMethod('getHeaders')
            ->setReturnType('array')
            ->addBody('return [\'Content-Type\' => ?];', [$contentType]);

        return $this->printer->printFile($file);
    }
}
