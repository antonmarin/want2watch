<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\OpenApiCodeGenerator\Generator;

use antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel\RequestDTO;
use cebe\openapi\spec\Operation;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;
use Nette\PhpGenerator\PsrPrinter;

final class Request
{
    private Printer $printer;

    public function __construct()
    {
        $this->printer = new PsrPrinter();
    }

    public function generateFile(
        Operation $operation,
        PhpNamespace $operationNamespace
    ): string {
        $file = new PhpFile();
        $file->setStrictTypes();
        $namespace = $file->addNamespace(clone $operationNamespace);
        $namespace->addUse(RequestDTO::class);
        $namespace->addUse('\Symfony\Component\Validator\Constraints', 'Assert');
        $class = $namespace->addClass('Request');
        $class
            ->setFinal()
            ->addImplement(RequestDTO::class);
        $constructor = $class->addMethod('__construct');
        $constructor->addParameter('request')->setType(\Symfony\Component\HttpFoundation\Request::class);
        foreach ($operation->parameters as $parameter) {
            $property = $class->addProperty($parameter->name)
                ->setPrivate()
                ->setType($parameter->schema->type)
                ->addComment(strtr('@var *type*', ['*type*' => $parameter->schema->type]));
            if ($parameter->required) {
                $property->addComment(
                    strtr(
                        '@Assert\NotBlank()',
                        ['*assert*' => $this->blankableRequired($parameter->schema->type) ? 'Blank' : 'Null']
                    )
                );
            }
            $constructor->addBody(
                strtr(
                    '$this->{propertyName} = ({type}) $request->query->get(\'{propertyName}\');',
                    ['{propertyName}' => $property->getName(), '{type}' => $parameter->schema->type]
                )
            );
            $class->addMethod('get' . ucwords($parameter->name))
                ->setReturnType($parameter->schema->type)
                ->addBody(strtr('return $this->{propertyName};', ['{propertyName}' => $property->getName()]));
        }

        return $this->printer->printFile($file);
    }

    private function blankableRequired(string $type): bool
    {
        switch ($type) {
            case 'string':
            case 'array':
            case 'bool':
                return true;
            default:
                return false;
        }
    }
}
