<?php

declare(strict_types=1);

namespace Infrastructure\Http\OpenApiCodeGenerator\Generator;

use Infrastructure\Http\SymfonyHttpKernel\RequestDTO;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\Parameter;
use cebe\openapi\spec\Schema;
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
        /** @var Parameter $parameter */
        foreach ($operation->parameters as $parameter) {
            /** @var Schema $schema */
            $schema = $parameter->schema;
            $property = $class->addProperty($parameter->name)
                ->setPrivate()
                ->setType($schema->type)
                ->addComment(strtr('@var *type*', ['*type*' => $schema->type]));
            if ($parameter->required) {
                $property->addComment(
                    strtr(
                        '@Assert\NotBlank()',
                        ['*assert*' => $this->blankableRequired($schema->type) ? 'Blank' : 'Null']
                    )
                );
            }
            $constructor->addBody(
                strtr(
                    '$this->{propertyName} = ({type}) $request->query->get(\'{propertyName}\');',
                    ['{propertyName}' => $property->getName(), '{type}' => $schema->type]
                )
            );
            $class->addMethod('get' . ucwords($parameter->name))
                ->setReturnType($schema->type)
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
